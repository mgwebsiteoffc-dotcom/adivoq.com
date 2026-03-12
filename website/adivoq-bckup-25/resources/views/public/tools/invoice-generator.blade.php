<!-- resources/views/public/tools/invoice-generator.blade.php -->
@extends('layouts.public')

@section('title', 'Free Invoice Generator for Creators — AdivoQ')
@section('meta_description', 'Generate professional GST-compliant invoices for free. Perfect for content creators and freelancers in India.')

@section('content')
<div class="py-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl sm:text-4xl font-bold text-white">Free Invoice Generator</h1>
            <p class="mt-4 text-surface-400 max-w-2xl mx-auto">Create professional GST-compliant invoices in seconds. Download as PDF — no signup required.</p>
        </div>

        {{-- Invoice Form --}}
        <div class="bg-surface-800 border border-surface-700 rounded-2xl p-6 sm:p-8"
             x-data="{
                from: { name: '', email: '', address: '', pan: '', gstin: '' },
                to: { name: '', email: '', address: '', gstin: '' },
                invoiceNumber: 'INV-001',
                issueDate: new Date().toISOString().split('T')[0],
                dueDate: '',
                items: [{ description: '', qty: 1, rate: 0 }],
                gstType: 'none',
                gstRate: 18,
                notes: '',

                addItem() {
                    this.items.push({ description: '', qty: 1, rate: 0 });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                },

                get subtotal() {
                    return this.items.reduce((sum, item) => sum + (parseFloat(item.qty) || 0) * (parseFloat(item.rate) || 0), 0);
                },

                get cgstAmount() {
                    return this.gstType === 'cgst_sgst' ? (this.subtotal * this.gstRate / 2) / 100 : 0;
                },

                get sgstAmount() {
                    return this.cgstAmount;
                },

                get igstAmount() {
                    return this.gstType === 'igst' ? (this.subtotal * this.gstRate) / 100 : 0;
                },

                get total() {
                    return this.subtotal + this.cgstAmount + this.sgstAmount + this.igstAmount;
                },

                formatNumber(num) {
                    return parseFloat(num || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                previewInvoice() {
                    alert('Preview feature coming soon! For now, use the Download PDF button.');
                },

                downloadPDF() {
                    alert('To download invoices as PDF, sign up for a free AdivoQ account!\n\nWith AdivoQ you get:\n• Professional invoice templates\n• Auto tax calculations\n• Payment tracking\n• Brand management\n\nAll free!');
                    window.location.href = '{{ route('register') }}';
                }
             }">

            <div class="grid lg:grid-cols-2 gap-8">

                {{-- Left Column: Your Details --}}
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-white border-b border-surface-700 pb-3">Your Details</h3>

                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1.5">Your Name / Business Name *</label>
                        <input type="text" x-model="from.name" required class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm" placeholder="John Creator">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1.5">Email</label>
                        <input type="email" x-model="from.email" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm" placeholder="john@example.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1.5">Address</label>
                        <textarea x-model="from.address" rows="2" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm resize-none" placeholder="123 Street, City, State"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-1.5">PAN</label>
                            <input type="text" x-model="from.pan" maxlength="10" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm uppercase" placeholder="ABCDE1234F">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-1.5">GSTIN</label>
                            <input type="text" x-model="from.gstin" maxlength="15" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm uppercase" placeholder="27ABCDE1234F1Z5">
                        </div>
                    </div>
                </div>

                {{-- Right Column: Client Details --}}
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-white border-b border-surface-700 pb-3">Bill To</h3>

                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1.5">Client / Company Name *</label>
                        <input type="text" x-model="to.name" required class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm" placeholder="Brand XYZ Pvt Ltd">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1.5">Email</label>
                        <input type="email" x-model="to.email" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm" placeholder="accounts@brand.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1.5">Address</label>
                        <textarea x-model="to.address" rows="2" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm resize-none" placeholder="456 Business Park, City, State"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1.5">Client GSTIN</label>
                        <input type="text" x-model="to.gstin" maxlength="15" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm uppercase" placeholder="29AABCB1234F1Z5">
                    </div>
                </div>
            </div>

            {{-- Invoice Details --}}
            <div class="mt-8 pt-8 border-t border-surface-700">
                <h3 class="text-lg font-semibold text-white mb-6">Invoice Details</h3>

                <div class="grid sm:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1.5">Invoice Number *</label>
                        <input type="text" x-model="invoiceNumber" required class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm" placeholder="INV-001">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1.5">Issue Date *</label>
                        <input type="date" x-model="issueDate" required class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1.5">Due Date</label>
                        <input type="date" x-model="dueDate" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm">
                    </div>
                </div>

                {{-- Line Items --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-medium text-surface-300">Items</label>
                        <button @click="addItem()" type="button" class="text-sm text-brand-400 hover:text-brand-300 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Item
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex gap-3 items-start bg-surface-700/30 rounded-lg p-3">
                                <div class="flex-1">
                                    <input type="text" x-model="item.description" placeholder="Description" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-3 py-2 text-surface-100 placeholder-surface-500 focus:border-brand-500 text-sm">
                                </div>
                                <div class="w-20">
                                    <input type="number" x-model="item.qty" placeholder="Qty" min="1" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-3 py-2 text-surface-100 placeholder-surface-500 focus:border-brand-500 text-sm text-center">
                                </div>
                                <div class="w-28">
                                    <input type="number" x-model="item.rate" placeholder="Rate" min="0" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-3 py-2 text-surface-100 placeholder-surface-500 focus:border-brand-500 text-sm text-right">
                                </div>
                                <div class="w-28 py-2 text-right text-surface-300 font-mono text-sm" x-text="'₹' + formatNumber(item.qty * item.rate)"></div>
                                <button @click="removeItem(index)" type="button" class="p-2 text-surface-500 hover:text-red-400" x-show="items.length > 1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Tax Settings --}}
                <div class="grid sm:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1.5">GST Type</label>
                        <select x-model="gstType" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 focus:border-brand-500 text-sm">
                            <option value="none">No GST</option>
                            <option value="igst">IGST (Different State)</option>
                            <option value="cgst_sgst">CGST + SGST (Same State)</option>
                        </select>
                    </div>
                    <div x-show="gstType !== 'none'">
                        <label class="block text-sm font-medium text-surface-300 mb-1.5">GST Rate (%)</label>
                        <select x-model="gstRate" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 focus:border-brand-500 text-sm">
                            <option value="5">5%</option>
                            <option value="12">12%</option>
                            <option value="18">18%</option>
                            <option value="28">28%</option>
                        </select>
                    </div>
                </div>

                {{-- Totals --}}
                <div class="bg-surface-700/30 rounded-xl p-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-surface-400">Subtotal</span>
                        <span class="text-white font-mono" x-text="'₹' + formatNumber(subtotal)"></span>
                    </div>
                    <template x-if="gstType === 'cgst_sgst'">
                        <div>
                            <div class="flex justify-between text-sm">
                                <span class="text-surface-400" x-text="'CGST (' + (gstRate/2) + '%)'"></span>
                                <span class="text-white font-mono" x-text="'₹' + formatNumber(cgstAmount)"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-surface-400" x-text="'SGST (' + (gstRate/2) + '%)'"></span>
                                <span class="text-white font-mono" x-text="'₹' + formatNumber(sgstAmount)"></span>
                            </div>
                        </div>
                    </template>
                    <template x-if="gstType === 'igst'">
                        <div class="flex justify-between text-sm">
                            <span class="text-surface-400" x-text="'IGST (' + gstRate + '%)'"></span>
                            <span class="text-white font-mono" x-text="'₹' + formatNumber(igstAmount)"></span>
                        </div>
                    </template>
                    <div class="flex justify-between text-lg font-semibold pt-2 border-t border-surface-600">
                        <span class="text-white">Total</span>
                        <span class="text-brand-400 font-mono" x-text="'₹' + formatNumber(total)"></span>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="mt-6">
                    <label class="block text-sm font-medium text-surface-300 mb-1.5">Notes / Payment Terms</label>
                    <textarea x-model="notes" rows="2" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm resize-none" placeholder="Bank details, payment terms, etc."></textarea>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-surface-700">
                <p class="text-surface-500 text-sm">Preview opens in new tab. Download as PDF.</p>
                <div class="flex gap-3">
                    <button @click="previewInvoice()" type="button" class="px-6 py-2.5 border border-surface-600 text-surface-300 font-medium rounded-lg hover:border-surface-500 hover:text-white transition-colors text-sm">
                        Preview
                    </button>
                    <button @click="downloadPDF()" type="button" class="px-6 py-2.5 bg-brand-600 text-white font-semibold rounded-lg hover:bg-brand-700 transition-colors text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download PDF
                    </button>
                </div>
            </div>
        </div>

        {{-- CTA --}}
        <div class="mt-12 text-center bg-gradient-to-r from-brand-600/20 to-indigo-600/20 border border-brand-500/20 rounded-2xl p-8">
            <h3 class="text-xl font-semibold text-white mb-2">Need more features?</h3>
            <p class="text-surface-400 mb-6">Save invoices, track payments, send reminders, and manage multiple brands with AdivoQ.</p>
            <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition-colors">
                Try AdivoQ Free →
            </a>
        </div>
    </div>
</div>
@endsection