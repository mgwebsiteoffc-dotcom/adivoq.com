<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $categories = ExpenseCategory::forTenant($tenantId)
            ->orderByRaw("tenant_id IS NULL") // global first
            ->orderBy('name')
            ->get();

        return view('tenant.expense-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        ExpenseCategory::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Category added.');
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $expenseCategory->update($request->only(['name','description']));

        return back()->with('success', 'Category updated.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $expenseCategory->delete();
        return back()->with('success', 'Category deleted.');
    }
}