<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['invoice.brand'])->latest('payment_date');

        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->paginate(20)->appends($request->query());

        $stats = [
            'this_month' => Payment::confirmed()->thisMonth()->sum('amount'),
            'this_year' => Payment::confirmed()->whereYear('payment_date', now()->year)->sum('amount'),
            'total' => Payment::confirmed()->sum('amount'),
        ];

        return view('tenant.payments.index', compact('payments', 'stats'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['invoice.brand', 'invoice.campaign']);
        return view('tenant.payments.show', compact('payment'));
    }
}