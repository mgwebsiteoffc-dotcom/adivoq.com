@extends('layouts.tenant')
@section('title', $invoice ? 'Edit Invoice' : 'Create Invoice')
@section('page_title', $invoice ? 'Edit Invoice #' . $invoice->invoice_number : 'Create New Invoice')

@section('content')
<div x-data="invoiceForm()" x-init="init()">

    <a href="{{ $invoice ? route('dashboard.invoices.show', $invoice) : route('dashboard.invoices.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-1"></i>Back
    </a>

    {{-- ✅ Show validation errors properly --}}
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl p-4">
            <p class="font-semibold text-sm mb-2">Please fix the following:</p>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $invoice ? route('dashboard.invoices.update', $invoice) : route('dashboard.invoices.store') }}"
          @submit="submitting = true">
        @csrf
        @if($invoice) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Brand & Campaign --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">
                        <i class="fas fa-building mr-2 text-indigo-500"></i>Client Details
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">
                                Brand / Client <span class="text-red-500">*</span>
                            </label>

                            <select name="brand_id"
                                    x-model="brandId"
                                    @change="onBrandChange()"
                                    required
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="">Select Brand</option>
                                @foreach($brands as $b)
                                    <option value="{{ $b->id }}"
                                            data-state-code="{{ $b->state_code }}"
                                            data-gstin="{{ $b->gstin }}"
                                            {{ old('brand_id', $selectedBrandId ?? '') == $b->id ? 'selected' : '' }}>
                                        {{ $b->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('brand_id')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">
                                Campaign <span class="text-gray-400">(optional)</span>
                            </label>

                            <select name="campaign_id"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="">None</option>
                                @foreach($campaigns as $c)
                                    <option value="{{ $c->id }}"
                                        {{ old('campaign_id', $selectedCampaignId ?? '') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }} ({{ $c->brand->name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- GST indicator --}}
                    <div x-show="gstType"
                         class="mt-3 p-3 rounded-lg text-xs font-medium"
                         :class="gstType === 'intra' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700'">
                        <i class="fas fa-info-circle mr-1"></i>
                        <span x-text="gstType === 'intra' ? 'Same State → CGST + SGST will apply' : 'Different State → IGST will apply'"></span>
                    </div>
                </div>

                {{-- Line Items --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">
                        <i class="fas fa-list mr-2 text-indigo-500"></i>Line Items
                    </h3>

                    {{-- Header --}}
                    <div class="hidden sm:grid grid-cols-12 gap-2 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <div class="col-span-5">Description</div>
                        <div class="col-span-2">HSN/SAC</div>
                        <div class="col-span-1 text-center">Qty</div>
                        <div class="col-span-2 text-right">Rate (₹)</div>
                        <div class="col-span-1 text-right">Amount</div>
                        <div class="col-span-1"></div>
                    </div>

                    <template x-for="(item, index) in items" :key="index">
                        <div class="grid grid-cols-12 gap-2 mb-3 items-start">
                            <div class="col-span-12 sm:col-span-5">
                                <input type="text"
                                       :name="'items['+index+'][description]'"
                                       x-model="item.description"
                                       required
                                       placeholder="Service description *"
                                       class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div class="col-span-4 sm:col-span-2">
                                <input type="text"
                                       :name="'items['+index+'][hsn_sac_code]'"
                                       x-model="item.hsn_sac_code"
                                       placeholder="HSN/SAC"
                                       class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div class="col-span-3 sm:col-span-1">
                                <input type="number"
                                       :name="'items['+index+'][quantity]'"
                                       x-model.number="item.quantity"
                                       step="0.01"
                                       min="0.01"
                                       required
                                       placeholder="1"
                                       class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm text-center focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div class="col-span-4 sm:col-span-2">
                                <input type="number"
                                       :name="'items['+index+'][unit_price]'"
                                       x-model.number="item.unit_price"
                                       step="0.01"
                                       min="0"
                                       required
                                       placeholder="0.00"
                                       class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm text-right focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div class="col-span-3 sm:col-span-1 flex items-center justify-end">
                                <span class="text-sm font-semibold text-gray-700"
                                      x-text="'₹' + formatNumber(item.quantity * item.unit_price)"></span>
                            </div>

                            <div class="col-span-2 sm:col-span-1 flex items-center justify-center">
                                <button type="button"
                                        @click="removeItem(index)"
                                        x-show="items.length > 1"
                                        class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </template>

                    <button type="button"
                            @click="addItem()"
                            class="mt-3 flex items-center px-4 py-2.5 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-medium hover:bg-indigo-100 transition">
                        <i class="fas fa-plus mr-2"></i>Add Line Item
                    </button>
                </div>

                {{-- Notes & Terms --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">
                        <i class="fas fa-sticky-note mr-2 text-indigo-500"></i>Notes & Terms
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Notes to Client</label>
                            <textarea name="notes" rows="3" placeholder="Thank you for your business!"
                                      class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">{{ old('notes', $invoice?->notes ?? $invoiceSetting?->default_notes) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Terms & Conditions</label>
                            <textarea name="terms_and_conditions" rows="3" placeholder="Payment terms, late fees, etc."
                                      class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">{{ old('terms_and_conditions', $invoice?->terms_and_conditions ?? $invoiceSetting?->default_terms_and_conditions) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Invoice Details --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Invoice Details</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Invoice Number</label>
                            <input type="text" value="{{ $nextNumber }}" disabled
                                   class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm font-mono text-gray-600">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Issue Date <span class="text-red-500">*</span></label>
                            <input type="date"
                                   name="issue_date"
                                   value="{{ old('issue_date', $invoice?->issue_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                                   required
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Payment Terms</label>
                            <select name="payment_terms"
                                    x-model="paymentTerms"
                                    @change="updateDueDate()"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                @foreach(config('invoicehero.payment_terms') as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('payment_terms', $invoice?->payment_terms ?? $invoiceSetting?->default_payment_terms) === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Due Date <span class="text-red-500">*</span></label>
                            <input type="date"
                                   name="due_date"
                                   x-model="dueDate"
                                   required
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                {{-- Tax & Discount --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Tax & Discount</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">GST Rate (%)</label>
                            <select name="gst_rate"
                                    x-model.number="gstRate"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                @foreach([0, 5, 12, 18, 28] as $rate)
                                    <option value="{{ $rate }}">{{ $rate }}%</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">TDS Rate (%)</label>
                            <select name="tds_rate"
                                    x-model.number="tdsRate"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="0">No TDS</option>
                                @foreach([1, 2, 5, 10, 20] as $rate)
                                    <option value="{{ $rate }}">{{ $rate }}%</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Discount Type</label>
                            <select name="discount_type"
                                    x-model="discountType"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="">No Discount</option>
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (₹)</option>
                            </select>
                        </div>

                        <div x-show="discountType">
                            <label class="block text-xs font-semibold text-gray-600 mb-1"
                                   x-text="discountType === 'percentage' ? 'Discount (%)' : 'Discount Amount (₹)'"></label>
                            <input type="number"
                                   name="discount_value"
                                   x-model.number="discountValue"
                                   step="0.01"
                                   min="0"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Summary</h3>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium" x-text="'₹' + formatNumber(subtotal)"></span>
                        </div>

                        <div x-show="discountAmount > 0" class="flex justify-between text-green-600">
                            <span>Discount</span>
                            <span x-text="'-₹' + formatNumber(discountAmount)"></span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Taxable Amount</span>
                            <span class="font-medium" x-text="'₹' + formatNumber(taxableAmount)"></span>
                        </div>

                        <template x-if="gstType === 'intra'">
                            <div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500" x-text="'CGST (' + (gstRate/2) + '%)'"></span>
                                    <span x-text="'₹' + formatNumber(cgstAmount)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500" x-text="'SGST (' + (gstRate/2) + '%)'"></span>
                                    <span x-text="'₹' + formatNumber(sgstAmount)"></span>
                                </div>
                            </div>
                        </template>

                        <template x-if="gstType === 'inter' || !gstType">
                            <div class="flex justify-between" x-show="igstAmount > 0">
                                <span class="text-gray-500" x-text="'IGST (' + gstRate + '%)'"></span>
                                <span x-text="'₹' + formatNumber(igstAmount)"></span>
                            </div>
                        </template>

                        <hr class="my-2">

                        <div class="flex justify-between text-base font-bold">
                            <span>Total</span>
                            <span class="text-indigo-600" x-text="'₹' + formatNumber(totalAmount)"></span>
                        </div>

                        <div x-show="tdsAmount > 0" class="flex justify-between text-red-600">
                            <span x-text="'TDS (' + tdsRate + '%)'"></span>
                            <span x-text="'-₹' + formatNumber(tdsAmount)"></span>
                        </div>

                        <div x-show="tdsAmount > 0" class="flex justify-between font-bold">
                            <span>Net Receivable</span>
                            <span class="text-green-600" x-text="'₹' + formatNumber(netReceivable)"></span>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="space-y-3">
                    <button type="submit"
                            :disabled="submitting"
                            class="w-full px-6 py-3 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg disabled:opacity-50">
                        <i class="fas fa-save mr-2"></i>
                        <span x-text="submitting ? 'Saving...' : '{{ $invoice ? 'Update Invoice' : 'Create Invoice' }}'"></span>
                    </button>

                    <a href="{{ route('dashboard.invoices.index') }}"
                       class="block text-center px-6 py-3 bg-gray-100 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition">
                        Cancel
                    </a>
                </div>
            </div>
        </div>

        <input type="hidden" name="currency" value="INR">
    </form>
</div>

{{-- ✅ FIX: prepare items + defaults in PHP (no fn() inside @json) --}}
@php
    $itemsData = old('items');

    if (!is_array($itemsData) || count($itemsData) === 0) {
        $itemsData = [];

        if (isset($invoice) && $invoice) {
            foreach ($invoice->items as $it) {
                $itemsData[] = [
                    'description' => (string) $it->description,
                    'hsn_sac_code' => (string) ($it->hsn_sac_code ?? ''),
                    'quantity' => (float) $it->quantity,
                    'unit_price' => (float) $it->unit_price,
                ];
            }
        }

        if (count($itemsData) === 0) {
            $itemsData[] = ['description' => '', 'hsn_sac_code' => '', 'quantity' => 1, 'unit_price' => 0];
        }
    }

    $brandIdDefault = old('brand_id', $selectedBrandId ?? '');

    $discountTypeDefault = old('discount_type', $invoice->discount_type ?? '');
    $discountValueDefault = old('discount_value', $invoice->discount_value ?? 0);

    $paymentTermsDefault = old('payment_terms', $invoice->payment_terms ?? ($invoiceSetting->default_payment_terms ?? 'net_30'));

    $dueDateDefault = old('due_date');
    if (!$dueDateDefault) {
        if (isset($invoice) && $invoice && $invoice->due_date) {
            $dueDateDefault = $invoice->due_date->format('Y-m-d');
        } else {
            $dueDateDefault = now()->addDays(30)->format('Y-m-d');
        }
    }

    // gstRate default: prefer old(), else invoice derived, else tenant default (IGST default rate)
    $gstRateDefault = old('gst_rate');
    if ($gstRateDefault === null || $gstRateDefault === '') {
        if (isset($invoice) && $invoice) {
            $gstRateDefault = ($invoice->cgst_rate > 0) ? ($invoice->cgst_rate * 2) : ($invoice->igst_rate ?? 0);
        } else {
            $gstRateDefault = $taxSetting->default_igst_rate ?? 18;
        }
    }

    $tdsRateDefault = old('tds_rate');
    if ($tdsRateDefault === null || $tdsRateDefault === '') {
        $tdsRateDefault = $invoice->tds_rate ?? 0;
    }

    $tenantStateCodeDefault = auth()->user()->tenant->state_code
        ?? (auth()->user()->tenant->taxSetting->state_code ?? '');
@endphp

@push('scripts')
<script>
function invoiceForm() {
    return {
        submitting: false,

        brandId: @json($brandIdDefault),
        items: @json($itemsData),

        gstRate: @json((float) $gstRateDefault),
        tdsRate: @json((float) $tdsRateDefault),

        discountType: @json($discountTypeDefault),
        discountValue: @json((float) $discountValueDefault),

        paymentTerms: @json($paymentTermsDefault),
        dueDate: @json($dueDateDefault),

        gstType: null,
        tenantStateCode: @json($tenantStateCodeDefault),

        init() {
            this.onBrandChange();
            if (!this.items || this.items.length === 0) this.addItem();
        },

        onBrandChange() {
            const select = document.querySelector('select[name="brand_id"]');
            if (!select) {
                this.gstType = null;
                return;
            }
            const option = select.options[select.selectedIndex];
            const brandState = option?.dataset?.stateCode || '';
            if (this.tenantStateCode && brandState) {
                this.gstType = (this.tenantStateCode === brandState) ? 'intra' : 'inter';
            } else {
                this.gstType = null;
            }
        },

        addItem() {
            this.items.push({ description: '', hsn_sac_code: '', quantity: 1, unit_price: 0 });
        },

        removeItem(i) {
            if (this.items.length > 1) this.items.splice(i, 1);
        },

        get subtotal() {
            return this.items.reduce((s, i) => s + (Number(i.quantity || 0) * Number(i.unit_price || 0)), 0);
        },

        get discountAmount() {
            if (!this.discountType) return 0;
            const sub = this.subtotal;
            const val = Number(this.discountValue || 0);
            return this.discountType === 'percentage' ? sub * (val / 100) : val;
        },

        get taxableAmount() {
            return Math.max(0, this.subtotal - this.discountAmount);
        },

        get cgstAmount() {
            return this.gstType === 'intra' ? this.taxableAmount * (Number(this.gstRate || 0) / 2 / 100) : 0;
        },

        get sgstAmount() {
            return this.gstType === 'intra' ? this.taxableAmount * (Number(this.gstRate || 0) / 2 / 100) : 0;
        },

        get igstAmount() {
            return this.gstType !== 'intra' ? this.taxableAmount * (Number(this.gstRate || 0) / 100) : 0;
        },

        get totalTax() {
            return this.cgstAmount + this.sgstAmount + this.igstAmount;
        },

        get totalAmount() {
            return this.taxableAmount + this.totalTax;
        },

        get tdsAmount() {
            return this.totalAmount * (Number(this.tdsRate || 0) / 100);
        },

        get netReceivable() {
            return this.totalAmount - this.tdsAmount;
        },

        formatNumber(n) {
            return Number(n || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        updateDueDate() {
            const days = { due_on_receipt: 0, net_7: 7, net_15: 15, net_30: 30, net_45: 45, net_60: 60 };
            const d = days[this.paymentTerms];

            if (d === undefined) return;

            const issueDateEl = document.querySelector('input[name="issue_date"]');
            const issueDate = issueDateEl?.value || new Date().toISOString().split('T')[0];

            const date = new Date(issueDate);
            date.setDate(date.getDate() + d);
            this.dueDate = date.toISOString().split('T')[0];
        }
    };
}
</script>
@endpush
@endsection