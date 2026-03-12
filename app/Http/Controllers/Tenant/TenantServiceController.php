<?php

namespace App\Http\Controllers\Tenant;

use App\Models\HsnSacCode;
use App\Models\TenantService;
use Illuminate\Http\Request;

class TenantServiceController
{
    /**
     * Display a listing of tenant services.
     */
    public function index()
    {
        $tenant = auth()->user()->tenant;
        $services = $tenant->services()
            ->with('hsnCode')
            ->orderBy('created_at', 'desc')
            ->get();

        // Return view for page load
        return view('tenant.settings.services.index', compact('services'));
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hsn_sac_code_id' => 'required|exists:hsn_sac_codes,id',
            'default_unit_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        $service = auth()->user()->tenant->services()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'data' => [
                'id' => $service->id,
                'name' => $service->name,
                'hsn_code' => $service->hsnCode->code,
                'default_unit_price' => $service->default_unit_price,
                'unit' => $service->unit,
                'tax_rate' => $service->tax_rate,
                'is_active' => $service->is_active,
            ],
        ], 201);
    }

    /**
     * Display the specified service.
     */
    public function show($service)
    {
        $service = auth()->user()->tenant->services()
            ->findOrFail($service);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'hsn_sac_code_id' => $service->hsn_sac_code_id,
                'hsn_code' => $service->hsnCode->code,
                'default_unit_price' => $service->default_unit_price,
                'unit' => $service->unit,
                'tax_rate' => $service->tax_rate,
                'is_active' => $service->is_active,
            ],
        ]);
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, $service)
    {
        $service = auth()->user()->tenant->services()
            ->findOrFail($service);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'hsn_sac_code_id' => 'nullable|exists:hsn_sac_codes,id',
            'default_unit_price' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $service->update(array_filter($validated, fn ($value) => !is_null($value)));

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'data' => [
                'id' => $service->id,
                'name' => $service->name,
                'hsn_code' => $service->hsnCode->code,
                'default_unit_price' => $service->default_unit_price,
                'unit' => $service->unit,
                'tax_rate' => $service->tax_rate,
                'is_active' => $service->is_active,
            ],
        ]);
    }

    /**
     * Remove the specified service.
     */
    public function destroy($service)
    {
        $service = auth()->user()->tenant->services()
            ->findOrFail($service);

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully',
        ]);
    }

    /**
     * Search and list HSN codes available for selection.
     */
    public function searchHsn(Request $request)
    {
        $search = $request->get('search', '');
        $applicable = $request->get('applicable_to', null);

        $query = HsnSacCode::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($applicable) {
            $query->applicableTo($applicable);
        }

        $codes = $query->take(50)->get();

        return response()->json([
            'success' => true,
            'data' => $codes->map(fn ($code) => [
                'id' => $code->id,
                'code' => $code->code,
                'description' => $code->description,
                'applicable_to' => $code->applicable_to,
                'display' => "{$code->code} - {$code->description}",
            ]),
        ]);
    }

    /**
     * API: Get all services as JSON (for Alpine.js)
     */
    public function apiList()
    {
        $services = auth()->user()->tenant->services()
            ->with('hsnCode')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $services->map(fn ($service) => [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'hsn_code' => $service->hsnCode->code,
                'hsn_description' => $service->hsnCode->description,
                'default_unit_price' => $service->default_unit_price,
                'unit' => $service->unit,
                'tax_rate' => $service->tax_rate,
                'is_active' => $service->is_active,
                'created_at' => $service->created_at->format('Y-m-d H:i'),
            ]),
        ]);
    }

    /**
     * API: Store service via AJAX
     */
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hsn_sac_code_id' => 'required|exists:hsn_sac_codes,id',
            'default_unit_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        $service = auth()->user()->tenant->services()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'data' => [
                'id' => $service->id,
                'name' => $service->name,
                'hsn_code' => $service->hsnCode->code,
                'default_unit_price' => $service->default_unit_price,
                'unit' => $service->unit,
                'tax_rate' => $service->tax_rate,
                'is_active' => $service->is_active,
            ],
        ], 201);
    }

    /**
     * API: Delete service via AJAX
     */
    public function apiDestroy($id)
    {
        $service = auth()->user()->tenant->services()
            ->findOrFail($id);

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully',
        ]);
    }
}