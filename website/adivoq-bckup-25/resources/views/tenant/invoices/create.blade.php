@extends('layouts.tenant')

@section('title', 'Generate Invoice')
@section('page_title', 'Generate Invoice')

@section('content')
<div class="max-w-3xl space-y-6">

<form method="POST"
      action="{{ route('tenant.invoices.store') }}"
      class="bg-surface-800 border border-surface-700 rounded-xl p-6 space-y-6">
    @csrf

    {{-- Brand --}}
    <div>
        <label class="block text-sm text-surface-400 mb-2">Brand *</label>
        <select id="brandSelect"
                required
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
            <option value="">Select Brand</option>
            @foreach($brands as $brand)
                <option value="{{ $brand->id }}">
                    {{ $brand->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Campaign --}}
    <div>
        <label class="block text-sm text-surface-400 mb-2">Campaign *</label>
        <select id="campaignSelect"
                name="campaign_id"
                required
                disabled
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
            <option value="">Select Campaign</option>
        </select>
    </div>

    {{-- Milestones --}}
    <div>
        <h3 class="text-white font-semibold mb-3">Completed Milestones</h3>
        <div id="milestoneContainer"
             class="space-y-2 text-sm text-surface-300">
            <p class="text-surface-500">Select campaign first.</p>
        </div>
    </div>

    <button type="submit"
            class="px-6 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
        Generate Invoice
    </button>

</form>

</div>

<script>
const brandSelect = document.getElementById('brandSelect');
const campaignSelect = document.getElementById('campaignSelect');
const milestoneContainer = document.getElementById('milestoneContainer');

// Load campaigns when brand changes
brandSelect.addEventListener('change', async function () {

    campaignSelect.innerHTML = '<option>Loading...</option>';
    campaignSelect.disabled = true;
    milestoneContainer.innerHTML = '';

    if (!this.value) return;

    const response = await fetch('/ajax/brand/' + this.value + '/campaigns');
    const campaigns = await response.json();

    campaignSelect.innerHTML = '<option value="">Select Campaign</option>';

    campaigns.forEach(campaign => {
        campaignSelect.innerHTML += `<option value="${campaign.id}">${campaign.title}</option>`;
    });

    campaignSelect.disabled = false;
});

// Load milestones when campaign changes
campaignSelect.addEventListener('change', async function () {

    milestoneContainer.innerHTML = '<p class="text-surface-500">Loading...</p>';

    if (!this.value) return;

    const response = await fetch('/ajax/campaign/' + this.value + '/milestones');
    const milestones = await response.json();

    if (!milestones.length) {
        milestoneContainer.innerHTML = '<p class="text-surface-500">No completed milestones available.</p>';
        return;
    }

    milestoneContainer.innerHTML = '';

    milestones.forEach(milestone => {
        milestoneContainer.innerHTML += `
            <label class="flex items-center gap-3">
                <input type="checkbox"
                       name="milestone_ids[]"
                       value="${milestone.id}"
                       required
                       class="accent-brand-500">
                ${milestone.title} — ₹${milestone.amount}
            </label>
        `;
    });
});
</script>
@endsection