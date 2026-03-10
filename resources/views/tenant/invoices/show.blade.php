@extends('layouts.tenant')
@section('title', 'Invoice #' . $invoice->invoice_number)
@section('page_title', 'Invoice #' . $invoice->invoice_number)

@section('content')
{{-- Status & Actions Bar --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center flex-wrap gap-2">
            <a href="{{ route('dashboard.invoices.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mr-2"><i class="fas fa-arrow-left mr-1"></i>Back</a>
            @php
                $statusColors = ['draft'=>'gray','sent'=>'blue','viewed'=>'purple','partially_paid'=>'yellow','paid'=>'green','overdue'=>'red','cancelled'=>'gray'];
                $sc = $statusColors[$invoice->status] ?? 'gray';
                if ($invoice->isOverdue()) $sc = 'red';
            @endphp
            <span class="px-3 py-1 text-xs font-bold rounded-full bg-{{ $sc }}-100 text-{{ $sc }}-700">
                <i class="fas fa-circle mr-1" style="font-size:5px;vertical-align:middle;"></i>
                {{ strtoupper($invoice->isOverdue() && !in_array($invoice->status, ['paid','cancelled']) ? 'OVERDUE' : str_replace('_',' ',$invoice->status)) }}
            </span>

            @if($invoice->is_recurring)
    <span class="px-3 py-1 text-xs font-bold rounded-full bg-indigo-100 text-indigo-700">
        <i class="fas fa-sync mr-1"></i>
        Recurring ({{ ucfirst($invoice->recurring_frequency) }}) • Next: {{ $invoice->next_recurring_date?->format('M d, Y') }}
    </span>
@endif
            <span class="text-sm text-gray-500">{{ $invoice->brand->name }}</span>
        </div>

        <div class="flex flex-wrap items-center gap-2">

        <a href="{{ route('dashboard.invoices.recurring.edit', $invoice) }}"
   class="px-3 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition">
    <i class="fas fa-sync mr-1"></i>Recurring
</a>
            {{-- PDF --}}
            <a href="{{ route('dashboard.invoices.pdf', $invoice) }}" class="px-3 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-download mr-1"></i>PDF
            </a>

            @if(!$invoice->isCancelled() && !$invoice->isPaid())
                {{-- Send Email --}}
                <form method="POST" action="{{ route('dashboard.invoices.send-email', $invoice) }}" class="inline">@csrf
                    <button class="px-3 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-envelope mr-1"></i>Email
                    </button>
                </form>

                {{-- Send WhatsApp --}}
                <form method="POST" action="{{ route('dashboard.invoices.send-whatsapp', $invoice) }}" class="inline">@csrf
                    <button class="px-3 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition">
                        <i class="fab fa-whatsapp mr-1"></i>WhatsApp
                    </button>
                </form>

                {{-- Payment Link --}}
                @if(!$invoice->payment_link_token)
                    <form method="POST" action="{{ route('dashboard.invoices.payment-link', $invoice) }}" class="inline">@csrf
                        <button class="px-3 py-2 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 transition">
                            <i class="fas fa-link mr-1"></i>Payment Link
                        </button>
                    </form>
                @endif

                {{-- Reminder --}}
                @if(!$invoice->isDraft())
                    <form method="POST" action="{{ route('dashboard.invoices.send-reminder', $invoice) }}" class="inline">@csrf
                        <button class="px-3 py-2 bg-orange-600 text-white text-xs font-medium rounded-lg hover:bg-orange-700 transition">
                            <i class="fas fa-bell mr-1"></i>Remind
                        </button>
                    </form>
                @endif
            @endif

            {{-- Duplicate --}}
            <form method="POST" action="{{ route('dashboard.invoices.duplicate', $invoice) }}" class="inline">@csrf
                <button class="px-3 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-copy mr-1"></i>Duplicate
                </button>
            </form>

            {{-- Edit (draft only) --}}
            @if($invoice->isDraft())
                <a href="{{ route('dashboard.invoices.edit', $invoice) }}" class="px-3 py-2 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
            @endif

            {{-- Cancel --}}
            @if(!$invoice->isPaid() && !$invoice->isCancelled())
                <form method="POST" action="{{ route('dashboard.invoices.cancel', $invoice) }}" class="inline" onsubmit="return confirm('Cancel this invoice?')">@csrf
                    <button class="px-3 py-2 bg-red-100 text-red-700 text-xs font-medium rounded-lg hover:bg-red-200 transition">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Payment Link Display --}}
    @if($invoice->payment_link_token)
        <div class="mt-3 p-3 bg-purple-50 rounded-lg flex items-center justify-between">
            <div class="flex items-center text-sm text-purple-800">
                <i class="fas fa-link mr-2"></i>
                <span class="font-mono text-xs truncate max-w-md">{{ route('payment.link', $invoice->payment_link_token) }}</span>
            </div>
            <button onclick="navigator.clipboard.writeText('{{ route('payment.link', $invoice->payment_link_token) }}'); this.innerHTML='<i class=\'fas fa-check mr-1\'></i>Copied!'; setTimeout(() => this.innerHTML='<i class=\'fas fa-copy mr-1\'></i>Copy', 2000)"
                    class="px-3 py-1.5 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-copy mr-1"></i>Copy
            </button>
        </div>
    @endif

    @if(session('payment_link'))
        <div class="mt-3 p-3 bg-green-50 rounded-lg text-sm text-green-800">
            <i class="fas fa-check-circle mr-1"></i>Payment link: <code class="font-mono text-xs">{{ session('payment_link') }}</code>
        </div>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Invoice Preview (2 cols) --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-200 p-6 lg:p-8">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:justify-between mb-8 gap-4">
                <div>
                    @if($invoiceSetting?->show_logo && $tenant->logo)
                        <img src="{{ asset('storage/' . $tenant->logo) }}" class="h-12 mb-3">
                    @endif
                    <h2 class="text-lg font-bold text-gray-900">{{ $tenant->business_name ?? $tenant->name }}</h2>
                    <div class="text-xs text-gray-500 mt-1">
                        @if($tenant->address_line1){{ $tenant->address_line1 }}<br>@endif
                        {{ $tenant->city }} {{ $tenant->state }} {{ $tenant->pincode }}<br>
                        @if($tenant->gstin)GSTIN: {{ $tenant->gstin }}<br>@endif
                        @if($tenant->pan_number)PAN: {{ $tenant->pan_number }}@endif
                    </div>
                </div>
                <div class="text-right">
                    <h1 class="text-3xl font-black" style="color: {{ $invoiceSetting?->invoice_color ?? '#4F46E5' }}">INVOICE</h1>
                    <p class="text-sm font-medium text-gray-600 mt-1">#{{ $invoice->invoice_number }}</p>
                    <div class="text-xs text-gray-500 mt-3 space-y-1">
                        <p><strong>Date:</strong> {{ $invoice->issue_date->format('d M Y') }}</p>
                        <p><strong>Due:</strong> {{ $invoice->due_date->format('d M Y') }}</p>
                        @if($invoice->reference_number)<p><strong>Ref:</strong> {{ $invoice->reference_number }}</p>@endif
                    </div>
                </div>
            </div>

            {{-- Bill To --}}
            <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-2">Bill To</p>
                <p class="text-sm font-bold text-gray-900">{{ $invoice->brand->name }}</p>
                <div class="text-xs text-gray-600 mt-1">
                    @if($invoice->brand->contact_person){{ $invoice->brand->contact_person }}<br>@endif
                    @if($invoice->brand->address_line1){{ $invoice->brand->full_address }}<br>@endif
                    @if($invoice->brand->email){{ $invoice->brand->email }}<br>@endif
                    @if($invoice->brand->gstin)GSTIN: {{ $invoice->brand->gstin }}@endif
                </div>
            </div>

            {{-- Items Table --}}
            <div class="overflow-x-auto mb-6">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-2" style="border-color: {{ $invoiceSetting?->invoice_color ?? '#4F46E5' }}">
                            <th class="text-left py-3 px-2 font-semibold text-gray-700">#</th>
                            <th class="text-left py-3 px-2 font-semibold text-gray-700">Description</th>
                            @if($invoice->items->first()?->hsn_sac_code)<th class="text-left py-3 px-2 font-semibold text-gray-700">HSN/SAC</th>@endif
                            <th class="text-center py-3 px-2 font-semibold text-gray-700">Qty</th>
                            <th class="text-right py-3 px-2 font-semibold text-gray-700">Rate</th>
                            <th class="text-right py-3 px-2 font-semibold text-gray-700">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $i => $item)
                            <tr class="border-b border-gray-100">
                                <td class="py-3 px-2 text-gray-500">{{ $i + 1 }}</td>
                                <td class="py-3 px-2 text-gray-800 font-medium">{{ $item->description }}</td>
                                @if($invoice->items->first()?->hsn_sac_code)<td class="py-3 px-2 text-gray-500 font-mono text-xs">{{ $item->hsn_sac_code }}</td>@endif
                                <td class="py-3 px-2 text-center text-gray-600">{{ $item->quantity }}</td>
                                <td class="py-3 px-2 text-right text-gray-600">₹{{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-3 px-2 text-right font-semibold text-gray-900">₹{{ number_format($item->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="flex justify-end">
                <div class="w-full sm:w-72 space-y-2 text-sm">
                    <div class="flex justify-between py-1"><span class="text-gray-500">Subtotal</span><span class="font-medium">₹{{ number_format($invoice->subtotal, 2) }}</span></div>
                    @if($invoice->discount_amount > 0)
                        <div class="flex justify-between py-1 text-green-600"><span>Discount {{ $invoice->discount_type === 'percentage' ? '(' . $invoice->discount_value . '%)' : '' }}</span><span>-₹{{ number_format($invoice->discount_amount, 2) }}</span></div>
                    @endif
                    @if($invoice->cgst_amount > 0)
                        <div class="flex justify-between py-1"><span class="text-gray-500">CGST ({{ $invoice->cgst_rate }}%)</span><span>₹{{ number_format($invoice->cgst_amount, 2) }}</span></div>
                        <div class="flex justify-between py-1"><span class="text-gray-500">SGST ({{ $invoice->sgst_rate }}%)</span><span>₹{{ number_format($invoice->sgst_amount, 2) }}</span></div>
                    @endif
                    @if($invoice->igst_amount > 0)
                        <div class="flex justify-between py-1"><span class="text-gray-500">IGST ({{ $invoice->igst_rate }}%)</span><span>₹{{ number_format($invoice->igst_amount, 2) }}</span></div>
                    @endif
                    <hr>
                    <div class="flex justify-between py-1 text-base font-bold" style="color: {{ $invoiceSetting?->invoice_color ?? '#4F46E5' }}">
                        <span>Total</span><span>₹{{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                    @if($invoice->tds_amount > 0)
                        <div class="flex justify-between py-1 text-red-600"><span>TDS ({{ $invoice->tds_rate }}%)</span><span>-₹{{ number_format($invoice->tds_amount, 2) }}</span></div>
                        <div class="flex justify-between py-1 font-bold"><span>Net Receivable</span><span class="text-green-700">₹{{ number_format($invoice->net_receivable, 2) }}</span></div>
                    @endif
                    @if($invoice->amount_paid > 0)
                        <div class="flex justify-between py-1 text-green-600"><span>Paid</span><span>-₹{{ number_format($invoice->amount_paid, 2) }}</span></div>
                    @endif
                    @if($invoice->amount_due > 0)
                        <div class="flex justify-between py-2 text-lg font-bold border-t-2 border-gray-800"><span>Amount Due</span><span class="text-red-600">₹{{ number_format($invoice->amount_due, 2) }}</span></div>
                    @elseif($invoice->isPaid())
                        <div class="flex justify-between py-2 text-lg font-bold border-t-2 border-green-500"><span>PAID</span><span class="text-green-600">✓</span></div>
                    @endif
                </div>
            </div>

            {{-- Bank Details --}}
            @if($bankDetails)
                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs font-bold text-gray-700 mb-2"><i class="fas fa-university mr-1"></i>Bank Details</p>
                    <div class="grid grid-cols-2 gap-1 text-xs text-gray-600">
                        <div>Bank: <strong>{{ $bankDetails->bank_name }}</strong></div>
                        <div>A/C: <strong>{{ $bankDetails->account_number }}</strong></div>
                        <div>IFSC: <strong>{{ $bankDetails->ifsc_code }}</strong></div>
                        <div>Name: <strong>{{ $bankDetails->account_holder_name }}</strong></div>
                        @if($bankDetails->upi_id)<div class="col-span-2">UPI: <strong>{{ $bankDetails->upi_id }}</strong></div>@endif
                    </div>
                </div>
            @endif

            {{-- Notes --}}
            @if($invoice->notes)
                <div class="mt-6"><p class="text-xs font-bold text-gray-700 mb-1">Notes</p><p class="text-xs text-gray-600">{{ $invoice->notes }}</p></div>
            @endif
            @if($invoice->terms_and_conditions)
                <div class="mt-4"><p class="text-xs font-bold text-gray-700 mb-1">Terms & Conditions</p><p class="text-xs text-gray-600">{{ $invoice->terms_and_conditions }}</p></div>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Record Payment --}}
        @if(!$invoice->isPaid() && !$invoice->isCancelled())
            <div class="bg-white rounded-xl border border-gray-200 p-6" x-data="{ showForm: false }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-900"><i class="fas fa-credit-card mr-2 text-green-500"></i>Record Payment</h3>
                    <button @click="showForm = !showForm" class="text-xs text-indigo-600 font-medium" x-text="showForm ? 'Close' : 'Add'"></button>
                </div>

                <div x-show="showForm" x-transition x-cloak>
                    <form method="POST" action="{{ route('dashboard.invoices.record-payment', $invoice) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Amount (₹) <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" value="{{ $invoice->amount_due }}" step="0.01" min="0.01" max="{{ $invoice->amount_due }}" required
                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                            <p class="text-xs text-gray-400 mt-0.5">Max: ₹{{ number_format($invoice->amount_due, 2) }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Date *</label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Method *</label>
                            <select name="payment_method" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="upi">UPI</option>
                                <option value="cash">Cash</option>
                                <option value="cheque">Cheque</option>
                                <option value="razorpay">Razorpay</option>
                                <option value="stripe">Stripe</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Reference / UTR</label>
                            <input type="text" name="transaction_reference" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Receipt</label>
                            <input type="file" name="receipt" accept="image/*,.pdf" class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-gray-100 file:text-gray-600">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Notes</label>
                            <input type="text" name="notes" placeholder="Optional" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-check mr-1"></i>Record Payment
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Payment History --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4"><i class="fas fa-history mr-2 text-indigo-500"></i>Payments ({{ $invoice->payments->count() }})</h3>
            @if($invoice->payments->count())
                <div class="space-y-3">
                    @foreach($invoice->payments as $pay)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <div>
                                <p class="text-sm font-semibold {{ $pay->status === 'confirmed' ? 'text-green-700' : 'text-yellow-700' }}">₹{{ number_format($pay->amount, 2) }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $pay->payment_method)) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">{{ $pay->payment_date->format('M d, Y') }}</p>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $pay->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ ucfirst($pay->status) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-500 text-center py-4">No payments yet.</p>
            @endif


        </div>

        {{-- Activity Log --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4"><i class="fas fa-stream mr-2 text-gray-400"></i>Activity Log</h3>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @forelse($invoice->activities as $act)
                    <div class="flex items-start py-2 border-b border-gray-50 last:border-0">
                        <div class="w-5 h-5 rounded-full bg-indigo-100 flex items-center justify-center mr-2 mt-0.5 flex-shrink-0">
                            @php
                                $icons = ['created'=>'fa-plus','updated'=>'fa-edit','sent_email'=>'fa-envelope','sent_whatsapp'=>'fa-whatsapp','payment_link_generated'=>'fa-link','pdf_downloaded'=>'fa-download','payment_recorded'=>'fa-check-circle','reminder_sent'=>'fa-bell','cancelled'=>'fa-times','duplicated'=>'fa-copy','viewed'=>'fa-eye','payment_submitted'=>'fa-credit-card'];
                            @endphp
                            <i class="fas {{ $icons[$act->action] ?? 'fa-circle' }} text-indigo-500" style="font-size:8px;"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-700"><span class="font-semibold capitalize">{{ str_replace('_', ' ', $act->action) }}</span></p>
                            @if($act->description)<p class="text-xs text-gray-500">{{ $act->description }}</p>@endif
                            <p class="text-xs text-gray-400">{{ $act->created_at->diffForHumans() }}{{ $act->user ? ' by ' . $act->user->name : '' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-500 text-center py-2">No activity.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection