<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Campaign;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('campaign')
            ->latest()
            ->paginate(20);

        return view('tenant.expenses.index', compact('expenses'));
    }

    public function create()
    {
        $campaigns = Campaign::orderBy('title')->get();

        return view('tenant.expenses.create', compact('campaigns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|string',
        ]);

        Expense::create([
            'campaign_id' => $request->campaign_id,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'currency' => 'INR',
            'expense_date' => $request->expense_date,
            'is_tax_deductible' => $request->has('is_tax_deductible'),
            'notes' => $request->notes,
        ]);

return redirect()->route('tenant.expenses')
            ->with('success', 'Expense added successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return back()->with('success', 'Expense deleted.');
    }
}