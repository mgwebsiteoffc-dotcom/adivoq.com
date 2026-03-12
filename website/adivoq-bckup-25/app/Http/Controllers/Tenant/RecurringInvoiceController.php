<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\RecurringInvoice;
use App\Models\Brand;
use Illuminate\Http\Request;

class RecurringInvoiceController extends Controller
{
    public function index()
    {
        $recurring = RecurringInvoice::with('brand')
            ->latest()
            ->paginate(20);

        return view('tenant.recurring.index', compact('recurring'));
    }

    public function create()
    {
        $brands = Brand::orderBy('name')->get();
        return view('tenant.recurring.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'title' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'frequency' => 'required|in:monthly,quarterly',
            'start_date' => 'required|date',
        ]);

        RecurringInvoice::create([
            'brand_id' => $request->brand_id,
            'title' => $request->title,
            'amount' => $request->amount,
            'frequency' => $request->frequency,
            'start_date' => $request->start_date,
            'next_run_date' => $request->start_date,
            'active' => true,
        ]);

        return redirect()->route('tenant.recurring.index')
            ->with('success', 'Recurring invoice created.');
    }

    public function destroy(RecurringInvoice $recurring)
    {
        $recurring->delete();
        return back()->with('success', 'Recurring invoice removed.');
    }

    public function toggle(RecurringInvoice $recurring)
    {
        $recurring->active = !$recurring->active;
        $recurring->save();

        return back()->with('success', 'Recurring invoice updated.');
    }
}