// public/assets/js/app.js

const AdivoQ = {
    formatCurrency(amount, currency = 'INR') {
        const symbols = { 'INR': '₹', 'USD': '$', 'EUR': '€', 'GBP': '£', 'AED': 'د.إ' };
        const symbol = symbols[currency] || currency + ' ';
        const formatted = parseFloat(amount || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return symbol + formatted;
    },

    async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (err) {
            return false;
        }
    },

    debounce(func, wait = 300) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func(...args), wait);
        };
    }
};

function invoiceCalculator() {
    return {
        items: [],
        discountType: 'percentage',
        discountValue: 0,
        gstType: 'igst',
        gstRate: 18,
        tdsRate: 0,
        currency: 'INR',

        addItem() {
            this.items.push({ description: '', hsn_sac_code: '', quantity: 1, unit_price: 0, amount: 0 });
        },
        removeItem(index) {
            this.items.splice(index, 1);
        },
        get subtotal() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.quantity || 0) * parseFloat(item.unit_price || 0)), 0);
        },
        get discountAmount() {
            return this.discountType === 'percentage'
                ? (this.subtotal * parseFloat(this.discountValue || 0)) / 100
                : parseFloat(this.discountValue || 0);
        },
        get taxableAmount() { return this.subtotal - this.discountAmount; },
        get cgstAmount() { return this.gstType === 'cgst_sgst' ? (this.taxableAmount * parseFloat(this.gstRate) / 2) / 100 : 0; },
        get sgstAmount() { return this.cgstAmount; },
        get igstAmount() { return this.gstType === 'igst' ? (this.taxableAmount * parseFloat(this.gstRate)) / 100 : 0; },
        get taxAmount() { return this.cgstAmount + this.sgstAmount + this.igstAmount; },
        get tdsAmount() { return (this.taxableAmount * parseFloat(this.tdsRate || 0)) / 100; },
        get totalAmount() { return this.taxableAmount + this.taxAmount; },
        get balanceDue() { return this.totalAmount - this.tdsAmount; },
    };
}

function taxCalculator() {
    return {
        income: 0, gstRate: 18, tdsSection: '194J', regime: 'new',
        tdsRates: { '194C': 2, '194J': 10, '194H': 5, '194O': 1 },
        get gstAmount() { return (parseFloat(this.income || 0) * this.gstRate) / 100; },
        get tdsRate() { return this.tdsRates[this.tdsSection] || 10; },
        get tdsAmount() { return (parseFloat(this.income || 0) * this.tdsRate) / 100; },
        get estimatedTax() {
            const annual = parseFloat(this.income || 0) * 12;
            if (this.regime === 'new') {
                if (annual <= 300000) return 0;
                if (annual <= 700000) return (annual - 300000) * 0.05;
                if (annual <= 1000000) return 20000 + (annual - 700000) * 0.10;
                if (annual <= 1200000) return 50000 + (annual - 1000000) * 0.15;
                if (annual <= 1500000) return 80000 + (annual - 1200000) * 0.20;
                return 140000 + (annual - 1500000) * 0.30;
            }
            if (annual <= 250000) return 0;
            if (annual <= 500000) return (annual - 250000) * 0.05;
            if (annual <= 1000000) return 12500 + (annual - 500000) * 0.20;
            return 112500 + (annual - 1000000) * 0.30;
        }
    };
}

window.AdivoQ = AdivoQ;
window.invoiceCalculator = invoiceCalculator;
window.taxCalculator = taxCalculator;