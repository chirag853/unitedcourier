<?php

namespace App\Services;

use App\Models\DisputeCharge;


class ShipmentChargeService
{
    public static function disputeCalculation($chargeType = '', $fwt = '', $twt = '', $destination = '', $apiType = '', $calType = '', $value = '')
    {
        $chargeType = trim((string) $chargeType);
        $key = strtolower(str_replace('_', ' ', $chargeType));

        // wt resolve: $value numeric ho to wahi, else $fwt; $twt bhi numeric ho to max.
        $wt = is_numeric($value) && (float) $value > 0 ? (float) $value : (float) $fwt;
        if (is_numeric($twt) && (float) $twt > $wt) {
            $wt = (float) $twt;
        }

        $destIn = strtoupper(trim((string) $destination));
        if ($destIn === '') {
            $destIn = 'ALL';
        }
        $apiIn = strtolower(trim((string) $apiType));
        $calIn = strtolower(trim((string) $calType));

        $noMatch = fn (string $reason): array => [
            'matched' => false,
            'charge_type' => $chargeType,
            'slab' => null,
            'amount' => 0.0,
            'gst_pct' => 0.0,
            'gst_amount' => 0.0,
            'total' => 0.0,
            'calculation_type' => null,
            'place_of_apply' => null,
            'destination' => null,
            'row_id' => null,
            'reason' => $reason,
        ];

        switch ($key) {
            // ---- STEP 0: chargeType check — CSB-V Sucharges (DB spelling + correct spelling dono) ----
            case 'csb-v sucharges':
            case 'csb-v surcharges':
                // ---- STEP 1: condition check — wt slab + customer type ----
                $isCargo = ($apiIn === 'cargo');
                if (! $isCargo) {
                    if ($wt <= 0) {
                        return $noMatch('wt missing');
                    }
                    if ($wt >= 0.05 && $wt <= 5) {
                        $slab = 'slab_0_5';
                    } elseif ($wt > 5 && $wt <= 20) {
                        $slab = 'slab_5_20';
                    } elseif ($wt > 20) {
                        $slab = 'slab_gt20';
                    } else {
                        return $noMatch('wt 0.05kg se kam: '.$wt.'kg');
                    }
                    // customer type check: sirf courier / ecommerce / ALL / empty pass
                    if (! in_array($apiIn, ['', 'all', 'courier', 'ecommerce'], true)) {
                        return $noMatch('customer type courier/ecommerce chahiye, mila: '.$apiIn);
                    }
                } else {
                    $slab = 'cargo';
                }

                // ---- STEP 2+3 ke liye DB row lao (values DB se, logic code se) ----
                $row = self::findDisputeRow($chargeType, $slab);

                // ---- STEP 2: destination check ----
                $rowDest = strtoupper(trim((string) ($row->destination ?? 'ALL')));
                if ($rowDest === '') {
                    $rowDest = 'ALL';
                }
                if ($rowDest !== 'ALL' && $destIn !== 'ALL' && $rowDest !== $destIn) {
                    return $noMatch('destination mismatch: row='.$rowDest.', input='.$destIn);
                }

                // ---- STEP 3: service_id + calculation_type check ----
                $rowSvc = strtolower(trim((string) ($row->service_id ?? 'all')));
                $isCustomerTypeWord = in_array($apiIn, ['', 'all', 'courier', 'ecommerce', 'cargo'], true);
                if ($rowSvc !== '' && $rowSvc !== 'all' && ! $isCustomerTypeWord && $rowSvc !== $apiIn) {
                    return $noMatch('service mismatch: row='.$rowSvc.', input='.$apiIn);
                }
                $rowCal = strtolower(trim((string) ($row->calculation_type ?? '')));
                if ($calIn !== '' && $calIn !== 'all' && $rowCal !== ''
                    && stripos($rowCal, $calIn) === false && stripos($calIn, $rowCal) === false) {
                    return $noMatch('calculation_type mismatch: row='.$rowCal.', input='.$calIn);
                }

                // ---- STEP 4: amount = values column, gst = gst_percentage column ----
                $amount = is_numeric($row->values ?? null) ? (float) $row->values : 0.0;
                $gstPct = (isset($row->gst_percentage) && is_numeric($row->gst_percentage))
                    ? (float) $row->gst_percentage
                    : 0.0;
                if ($amount <= 0 || $gstPct <= 0) {
                    // Purana text format ("Rs 50 + 18 % GST") ho to parse karo.
                    $parsed = self::parseDisputeValues((string) ($row->values ?? ''));
                    if ($amount <= 0) {
                        $amount = $parsed['amount'];
                    }
                    if ($gstPct <= 0) {
                        $gstPct = $parsed['gst_pct'];
                    }
                }
                if ($amount <= 0) {
                    // DB row na mile to hardcoded fallback (same slab)
                    [$amount, $gstPct] = match ($slab) {
                        'slab_0_5' => [50.0, 18.0],
                        'slab_5_20' => [100.0, 18.0],
                        'slab_gt20' => [200.0, 18.0],
                        default => [1000.0, 18.0],
                    };
                }
                $gstAmt = round($amount * $gstPct / 100, 2);

                return [
                    'matched' => true,
                    'charge_type' => $chargeType,
                    'slab' => $slab,
                    'amount' => round($amount, 2),
                    'gst_pct' => round($gstPct, 2),
                    'gst_amount' => $gstAmt,
                    'total' => round($amount + $gstAmt, 2),
                    'calculation_type' => $row->calculation_type ?? 'flat/awb',
                    'place_of_apply' => $row->place_of_apply ?? 'Shipment creation',
                    'destination' => $row->destination ?? 'ALL',
                    'row_id' => $row->id ?? null,
                    'reason' => 'ok',
                ];

            default:
                return $noMatch('unknown chargeType: '.$chargeType);
        }
    }


    
    protected static function findDisputeRow(string $chargeType, string $slab): ?object
    {
        try {
            // Space/underscore/spelling-proof lookup.
            $variants = [];
            foreach ([$chargeType, 'CSB-V Sucharges', 'CSB-V Surcharges'] as $v) {
                $v = trim((string) $v);
                if ($v === '') {
                    continue;
                }
                $variants[] = $v;
                $variants[] = str_replace(' ', '_', $v);
                $variants[] = str_replace('_', ' ', $v);
            }
            $rows = DisputeCharge::whereIn('additional_charges', array_values(array_unique($variants)))->get();
        } catch (\Throwable) {
            return null;
        }
        if ($rows->isEmpty()) {
            return null;
        }
        foreach ($rows as $r) {
            $c = strtolower((string) ($r->conditions ?? ''));
            $hit = match ($slab) {
                'cargo' => str_contains($c, 'cargo'),
                'slab_0_5' => str_contains($c, '0.05') && str_contains($c, '5 kg'),
                'slab_5_20' => str_contains($c, '5 kg') && str_contains($c, '20 kg'),
                'slab_gt20' => str_contains($c, '> 20') || str_contains($c, '>20'),
                default => false,
            };
            if ($hit) {
                return $r;
            }
        }

        return $rows->first();
    }

    /**
     * "Rs 50 + 18 % GST" -> [amount=50, gst_pct=18].
     */
    protected static function parseDisputeValues(string $values): array
    {
        $amount = 0.0;
        $gst = 0.0;
        if (preg_match('/Rs\.?\s*(\d+(?:\.\d+)?)/i', $values, $m)) {
            $amount = (float) $m[1];
        } elseif (preg_match('/(\d+(?:\.\d+)?)/', $values, $m)) {
            $amount = (float) $m[1];
        }
        if (preg_match('/(\d+(?:\.\d+)?)\s*%\s*GST/i', $values, $m)) {
            $gst = (float) $m[1];
        }

        return ['amount' => $amount, 'gst_pct' => $gst];
    }
}
