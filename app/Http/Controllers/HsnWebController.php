<?php

namespace App\Http\Controllers;

use App\Models\HsnSacCode;
use Illuminate\Http\Request;

class HsnWebController
{
    /**
     * Display HSN search page.
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        $filter = $request->get('filter', 'all'); // all, goods, service

        $query = HsnSacCode::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($filter === 'goods') {
            $query->applicableTo('Goods');
        } elseif ($filter === 'service') {
            $query->applicableTo('Service');
        }

        $codes = $query->orderBy('code')
            ->paginate(20)
            ->appends($request->query());

        return view('hsn.search', [
            'codes' => $codes,
            'search' => $search,
            'filter' => $filter,
        ]);
    }

    /**
     * Display HSN detail page.
     */
    public function show(HsnSacCode $hsn)
    {
        // Get related HSN codes (same applicable_to, ordered randomly)
        $relatedCodes = HsnSacCode::where('code', '!=', $hsn->code)
            ->where('applicable_to', $hsn->applicable_to)
            ->inRandomOrder()
            ->limit(5)
            ->get();

        return view('hsn.detail', [
            'hsn' => $hsn,
            'relatedCodes' => $relatedCodes,
        ]);
    }
}
