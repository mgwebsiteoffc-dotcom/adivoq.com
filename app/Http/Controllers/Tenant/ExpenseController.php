<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Campaign;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['category','campaign'])->latest('expense_date');

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }

        if ($request->filled('deductible')) {
            $query->where('is_tax_deductible', (bool) $request->deductible);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->paginate(20)->appends($request->query());

        $tenantId = auth()->user()->tenant_id;
        $categories = ExpenseCategory::forTenant($tenantId)->orderBy('name')->get();
        $campaigns = Campaign::orderBy('name')->get();

        $stats = [
            'this_month' => Expense::whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount'),
            'this_year' => Expense::whereYear('expense_date', now()->year)->sum('amount'),
        ];

        return view('tenant.expenses.index', compact('expenses','categories','campaigns','stats'));
    }

    public function create()
    {
        $tenantId = auth()->user()->tenant_id;
        $categories = ExpenseCategory::forTenant($tenantId)->orderBy('name')->get();
        $campaigns = Campaign::orderBy('name')->get();

        return view('tenant.expenses.form', [
            'expense' => null,
            'categories' => $categories,
            'campaigns' => $campaigns,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'is_tax_deductible' => 'nullable|boolean',
            'notes' => 'nullable|string|max:2000',
            'receipt' => 'nullable|file|max:2048',
        ]);

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('expense-receipts', 'public');
        }

        $data['is_tax_deductible'] = $request->boolean('is_tax_deductible');

        Expense::create($data);

        return redirect()->route('dashboard.expenses.index')->with('success', 'Expense added.');
    }

    public function show(Expense $expense)
    {
        $expense->load(['category','campaign']);
        return view('tenant.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $tenantId = auth()->user()->tenant_id;
        $categories = ExpenseCategory::forTenant($tenantId)->orderBy('name')->get();
        $campaigns = Campaign::orderBy('name')->get();

        return view('tenant.expenses.form', compact('expense','categories','campaigns'));
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'is_tax_deductible' => 'nullable|boolean',
            'notes' => 'nullable|string|max:2000',
            'receipt' => 'nullable|file|max:2048',
        ]);

        if ($request->hasFile('receipt')) {
            if ($expense->receipt_path) \Storage::disk('public')->delete($expense->receipt_path);
            $data['receipt_path'] = $request->file('receipt')->store('expense-receipts', 'public');
        }

        $data['is_tax_deductible'] = $request->boolean('is_tax_deductible');

        $expense->update($data);

        return redirect()->route('dashboard.expenses.show', $expense)->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->receipt_path) \Storage::disk('public')->delete($expense->receipt_path);
        $expense->delete();

        return redirect()->route('dashboard.expenses.index')->with('success', 'Expense deleted.');
    }
}