<?php

namespace App\Http\Controllers;

use App\Models\HsnSacCode;
use Illuminate\Http\Request;

class HsnSacCodeController
{
    /**
     * API: Search HSN codes with pagination.
     */
    public function search(Request $request)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 20);
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

        $total = $query->count();
        $codes = $query->orderBy('code')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $codes->map(fn ($code) => [
                'id' => $code->id,
                'code' => $code->code,
                'description' => $code->description,
                'applicable_to' => $code->applicable_to,
                'display' => "{$code->code} - {$code->description}",
            ]),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
            ],
        ]);
    }

    /**
     * Get a single HSN code by ID.
     */
    public function show($id)
    {
        $code = HsnSacCode::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $code->id,
                'code' => $code->code,
                'description' => $code->description,
                'applicable_to' => $code->applicable_to,
                'notes' => $code->notes,
                'created_at' => $code->created_at->format('Y-m-d'),
            ],
        ]);
    }
}
