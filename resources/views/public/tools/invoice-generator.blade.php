@extends('layouts.public')
@section('title', 'Free Invoice Generator — InvoiceHero')

@section('content')
<section class="py-12 lg:py-16" x-data="invoiceGenerator()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-10">
            <span class="inline-block px-4 py-1.5 bg-green-50 text-green-700 text-sm font-semibold rounded-full border border-green-200 mb-4">100% FREE</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900">Free <span class="gradient-text">Invoice Generator</span></h1>
            <p class="mt-3 text-gray-600">Create a professional invoice and download as PDF. No login required.</p>
        </div>

        <form method="POST" action="{{ route('tools.generate-pdf') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- From --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center">
                        <div class="w-8 h-8 bg-brand-100 rounded-lg flex items-center justify-center mr-2"><i class="fas fa-user text-brand-600 text-xs"></i></div>
                        From (You)
                    </h3>
                    <div class="space-y-3">
                        <input type="text" name="from_name" placeholder="Your Name / Business Name *" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                        <input type="email" name="from_email" placeholder="Email" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                        <input type="text" name="from_phone" placeholder="Phone" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                        <textarea name="from_address" rows="2" placeholder="Address" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500"></textarea>
                        <input type="text" name="from_gstin" placeholder="GSTIN (optional)" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>

                {{-- To --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-2"><i class="fas fa-building text-purple-600 text-xs"></i></div>
                        To (Client / Brand)
                    </h3>
                    <div class="space-y-3">
                        <input type="text" name="to_name" placeholder="Brand / Client Name *" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                        <input type="email" name="to_email" placeholder="Email" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                        <input type="text" name="to_phone" placeholder="Phone" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                        <textarea name="to_address" rows="2" placeholder="Address" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500"></textarea>
                        <input type="text" name="to_gstin" placeholder="GSTIN (optional)" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>
            </div>

            {{-- Invoice Details --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4">Invoice Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Invoice Number *</label>
                        <input type="text" name="invoice_number" value="INV-001" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Invoice Date *</label>
                        <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Due Date *</label>
                        <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>
            </div>

            {{-- Line Items --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4">Line Items</h3>

                <template x-for="(item, index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-3 mb-3 items-start">
                        <div class="col-span-12 sm:col-span-5">
                            <input type="text" :name="'items['+index+'][description]'" x-model="item.description" placeholder="Description *" required
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div class="col-span-4 sm:col-span-2">
                            <input type="number" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" placeholder="Qty" step="0.01" min="0.01" required
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div class="col-span-5 sm:col-span-3">
                            <input type="number" :name="'items['+index+'][rate]'" x-model.number="item.rate" placeholder="Rate (₹)" step="0.01" min="0" required
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div class="col-span-2 sm:col-span-1 flex items-center justify-center pt-1">
                            <span class="text-sm font-semibold text-gray-700" x-text="'₹' + (item.quantity * item.rate).toLocaleString('en-IN')"></span>
                        </div>
                        <div class="col-span-1 flex items-center justify-center pt-1">
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-red-400 hover:text-red-600">
                                <i class="fas fa-trash-alt text-sm"></i>
                            </button>
                        </div>
                    </div>
                </template>

                <button type="button" @click="addItem()" class="mt-3 px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-200 transition">
                    <i class="fas fa-plus mr-1"></i>Add Item
                </button>

                <div class="mt-6 flex flex-col items-end">
                    <div class="w-full sm:w-72 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-600">Subtotal</span><span class="font-medium" x-text="'₹' + subtotal.toLocaleString('en-IN')"></span></div>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-600">Tax</span>
                            <select name="tax_rate" x-model.number="taxRate" class="px-2 py-1 border border-gray-200 rounded-lg text-xs">
                                <option value="0">No Tax</option>
                                <option value="5">5%</option>
                                <option value="12">12%</option>
                                <option value="18">18%</option>
                                <option value="28">28%</option>
                            </select>
                            <span class="ml-auto font-medium" x-text="'₹' + taxAmount.toLocaleString('en-IN')"></span>
                        </div>
                        <hr>
                        <div class="flex justify-between text-base font-bold"><span>Total</span><span class="text-brand-600" x-text="'₹' + total.toLocaleString('en-IN')"></span></div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <label class="block text-sm font-bold text-gray-900 mb-2">Notes (Optional)</label>
                <textarea name="notes" rows="3" placeholder="Thank you for your business!" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500"></textarea>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-4">
                <button type="submit" class="w-full sm:w-auto px-10 py-4 gradient-bg text-white text-lg font-bold rounded-2xl shadow-xl hover:shadow-2xl transition">
                    <i class="fas fa-download mr-2"></i>Download Invoice PDF
                </button>
                <p class="text-sm text-gray-500">Free • No signup • No watermark</p>
            </div>
        </form>

        {{-- Upgrade CTA --}}
        <div class="mt-12 bg-gray-50 rounded-2xl p-8 border border-gray-200 text-center">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Want more features?</h3>
            <p class="text-gray-600 text-sm mb-5">Auto GST calculation, payment tracking, WhatsApp reminders, beautiful templates & more.</p>
            <a href="{{ route('register') }}" class="inline-block px-8 py-3 gradient-bg text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition">
                Create Free Account <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

@push('scripts')
<script>
function invoiceGenerator() {
    return {
        items: [{ description: '', quantity: 1, rate: 0 }],
        taxRate: 0,
        get subtotal() { return this.items.reduce((sum, item) => sum + (item.quantity * item.rate), 0); },
        get taxAmount() { return Math.round(this.subtotal * (this.taxRate / 100)); },
        get total() { return this.subtotal + this.taxAmount; },
        addItem() { this.items.push({ description: '', quantity: 1, rate: 0 }); },
        removeItem(index) { if (this.items.length > 1) this.items.splice(index, 1); },
    }
}
</script>
@endpush
@endsection