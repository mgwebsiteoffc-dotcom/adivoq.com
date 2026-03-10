<?php

namespace App\Services;

class TaxCalculatorService
{
    /**
     * Calculate GST based on states
     */
    public function calculateGST(
        float $amount,
        float $gstRate,
        string $sellerStateCode,
        string $buyerStateCode
    ): array {
        $sameState = $sellerStateCode === $buyerStateCode;

        if ($sameState) {
            $halfRate = $gstRate / 2;
            $cgst = round($amount * ($halfRate / 100), 2);
            $sgst = round($amount * ($halfRate / 100), 2);
            return [
                'type' => 'intra_state',
                'cgst_rate' => $halfRate,
                'cgst_amount' => $cgst,
                'sgst_rate' => $halfRate,
                'sgst_amount' => $sgst,
                'igst_rate' => 0,
                'igst_amount' => 0,
                'total_gst' => $cgst + $sgst,
            ];
        }

        $igst = round($amount * ($gstRate / 100), 2);
        return [
            'type' => 'inter_state',
            'cgst_rate' => 0,
            'cgst_amount' => 0,
            'sgst_rate' => 0,
            'sgst_amount' => 0,
            'igst_rate' => $gstRate,
            'igst_amount' => $igst,
            'total_gst' => $igst,
        ];
    }

    /**
     * Calculate TDS
     */
    public function calculateTDS(float $amount, float $tdsRate): array
    {
        $tdsAmount = round($amount * ($tdsRate / 100), 2);
        return [
            'tds_rate' => $tdsRate,
            'tds_amount' => $tdsAmount,
            'net_amount' => $amount - $tdsAmount,
        ];
    }

    /**
     * Calculate income tax (for public tool)
     */
    public function calculateIncomeTax(float $income, string $regime = 'new', string $fy = '2024-25'): array
    {
        if ($regime === 'old') {
            return $this->calculateOldRegime($income);
        }
        return $this->calculateNewRegime($income);
    }

    protected function calculateNewRegime(float $income): array
    {
        // FY 2024-25 new regime slabs
        $slabs = [
            ['min' => 0, 'max' => 300000, 'rate' => 0],
            ['min' => 300000, 'max' => 700000, 'rate' => 5],
            ['min' => 700000, 'max' => 1000000, 'rate' => 10],
            ['min' => 1000000, 'max' => 1200000, 'rate' => 15],
            ['min' => 1200000, 'max' => 1500000, 'rate' => 20],
            ['min' => 1500000, 'max' => PHP_FLOAT_MAX, 'rate' => 30],
        ];

        return $this->computeSlabTax($income, $slabs, 'New Regime');
    }

    protected function calculateOldRegime(float $income): array
    {
        $slabs = [
            ['min' => 0, 'max' => 250000, 'rate' => 0],
            ['min' => 250000, 'max' => 500000, 'rate' => 5],
            ['min' => 500000, 'max' => 1000000, 'rate' => 20],
            ['min' => 1000000, 'max' => PHP_FLOAT_MAX, 'rate' => 30],
        ];

        return $this->computeSlabTax($income, $slabs, 'Old Regime');
    }

    protected function computeSlabTax(float $income, array $slabs, string $regime): array
    {
        $tax = 0;
        $breakdown = [];

        foreach ($slabs as $slab) {
            if ($income <= $slab['min']) break;

            $taxableInSlab = min($income, $slab['max']) - $slab['min'];
            $slabTax = $taxableInSlab * ($slab['rate'] / 100);
            $tax += $slabTax;

            $breakdown[] = [
                'range' => '₹' . number_format($slab['min']) . ' - ₹' . ($slab['max'] >= PHP_FLOAT_MAX ? 'Above' : number_format($slab['max'])),
                'rate' => $slab['rate'] . '%',
                'taxable' => $taxableInSlab,
                'tax' => $slabTax,
            ];
        }

        // Cess (4% health & education cess)
        $cess = round($tax * 0.04, 2);
        $totalTax = round($tax + $cess, 2);

        return [
            'regime' => $regime,
            'income' => $income,
            'tax_before_cess' => round($tax, 2),
            'cess' => $cess,
            'total_tax' => $totalTax,
            'effective_rate' => $income > 0 ? round(($totalTax / $income) * 100, 2) : 0,
            'breakdown' => $breakdown,
        ];
    }
}