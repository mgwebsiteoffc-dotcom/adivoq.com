<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Milestone;
use App\Models\Invoice;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    private function canLinkInvoice(): bool
    {
        // Only owner/manager should link milestones to invoices
        return in_array(auth()->user()->role, ['owner', 'manager']);
    }

    private function validateInvoiceForCampaign(?int $invoiceId, Campaign $campaign): ?int
    {
        if (!$invoiceId) return null;

        $invoice = Invoice::where('id', $invoiceId)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();

        if (!$invoice) {
            abort(422, 'Invalid invoice selected.');
        }

        // Ensure invoice belongs to same campaign (strict)
        if ((int)$invoice->campaign_id !== (int)$campaign->id) {
            abort(422, 'Invoice must belong to the same campaign to link.');
        }

        // Prevent linking to cancelled invoices
        if ($invoice->isCancelled()) {
            abort(422, 'Cannot link milestone to cancelled invoice.');
        }

        return $invoice->id;
    }

    public function store(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'invoice_id' => 'nullable|integer',
        ]);

        $invoiceId = null;
        if ($request->filled('invoice_id')) {
            if (!$this->canLinkInvoice()) abort(403, 'Only owner/manager can link invoices.');
            $invoiceId = $this->validateInvoiceForCampaign((int)$request->invoice_id, $campaign);
        }

        $maxOrder = $campaign->milestones()->max('sort_order') ?? 0;

        $campaign->milestones()->create([
            'tenant_id' => auth()->user()->tenant_id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'] ?? 0,
            'due_date' => $validated['due_date'] ?? null,
            'sort_order' => $maxOrder + 1,
            'invoice_id' => $invoiceId,
        ]);

        return back()->with('success', 'Milestone added!');
    }

    public function update(Request $request, Milestone $milestone)
    {
        $campaign = $milestone->campaign;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:pending,in_progress,completed',
            'invoice_id' => 'nullable',
        ]);

        $update = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'] ?? 0,
            'due_date' => $validated['due_date'] ?? null,
        ];

        if (!empty($validated['status'])) {
            $update['status'] = $validated['status'];
            if ($validated['status'] === 'completed') {
                $update['completed_at'] = now();
            }
        }

        // Invoice linking (owner/manager only)
        if ($request->has('invoice_id')) {
            if (!$this->canLinkInvoice()) abort(403, 'Only owner/manager can link invoices.');

            $invoiceId = $request->invoice_id ? (int) $request->invoice_id : null;
            $update['invoice_id'] = $this->validateInvoiceForCampaign($invoiceId, $campaign);
        }

        $milestone->update($update);

        return back()->with('success', 'Milestone updated!');
    }

    public function destroy(Milestone $milestone)
    {
        $milestone->delete();
        return back()->with('success', 'Milestone deleted.');
    }

    public function complete(Milestone $milestone)
    {
        $milestone->markComplete();
        return back()->with('success', 'Milestone marked as complete!');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'milestones' => 'required|array',
            'milestones.*.id' => 'required|exists:milestones,id',
            'milestones.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->milestones as $item) {
            Milestone::where('id', $item['id'])
                ->where('tenant_id', auth()->user()->tenant_id)
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true]);
    }
}