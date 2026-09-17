<?php

namespace App\Services;

use App\Models\DisputeCharge;


class ShipmentChargeService
{
    public static function disputeCalculation($chargeType = '', $fwt = '', $twt = '', $destination = '', $apiType = '', $calType = '', $value = '', $dims = [], $actual = [], $service = '')
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
                return self::evaluateCsbV($chargeType, $wt, $destIn, $apiIn, $calIn);

            case 'additional handling surcharge':
                // USA + UPS + declared weight/dims (koi ek sub-condition match).
                $res = self::evaluateHandlingCharge($chargeType, $wt, $destIn, $service, $dims);
                return $res ?? $noMatch('koi handling condition match nahi hui');

            case 'large packet surcharge':
                // USA/UPS ya EU-UK/DPD + declared weight/dims.
                $res = self::evaluateLargePacket($chargeType, $wt, $destIn, $service, $dims);
                return $res ?? $noMatch('koi large-packet condition match nahi hui');

            case 'non-conveyable sucharges':
            case 'non-conveyable surcharges':
                // Scan operator confirmation flag par match ($actual['non_conveyable']).
                $res = self::evaluateNonConveyable($chargeType, $destIn, $service, $actual);
                return $res ?? $noMatch('extra large boxes: scan confirmation chahiye');

            case 'oversize sucharges':
            case 'oversize surcharges':
                // Actual dims > declared dims — scan data chahiye.
                $res = self::evaluateOversize($chargeType, $dims, $actual, $destIn, $service);
                return $res ?? $noMatch('oversize ke liye actual scan dims chahiye');

            case 'weight dispute':
                // Actual wt > declared wt — scan weight needed. Amount is custom (manual).
                $res = self::evaluateWeightDispute($chargeType, $wt, $actual, $destIn, $service);
                return $res ?? $noMatch('weight dispute needs an actual scan weight');

            case 'ddp sucharges':
            case 'ddp surcharges':
                // Tariff type DDP selected at creation (passed via $actual['incoterms']).
                $res = self::evaluateDdp($chargeType, $destIn, $service, $actual);
                return $res ?? $noMatch('DDP surcharge needs incoterms DDP');

            case 'go green plus charges':
                // DHL service, per-kg charge on ceil(chargeable weight).
                $res = self::evaluateGoGreen($chargeType, $wt, $destIn, $service);
                return $res ?? $noMatch('Go Green needs DHL service and weight');

            case 'weighing at first scan':
                // Master case: TABLE KE SAARE charge types evaluate karo
                // (CSB-V samet), jo-jo match ho sab return karo.
                return self::evaluateFirstScan($chargeType, $wt, $destIn, $apiIn, $calIn, $service, $dims, $actual);

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
            $rows = DisputeCharge::whereIn('additional_charges', array_values(array_unique($variants)))->where('status', 1)->get();
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

        // Strict slab match: slab ki row disabled ya missing ho to koi charge nahi.
        // Pehle yahan $rows->first() tha jo 1kg par 5-20 wali row utha leta tha.
        return null;
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

    /**
     * DB row se amount + gst nikaalo (values column, text format, slab fallback).
     * $skipFallback = true ho (custom amount) to fallback nahi lagta.
     * @return array [amount, gstPct]
     */
    protected static function resolveRowAmounts(?object $row, string $slab, bool $skipFallback = false): array
    {
        $valuesText = (string) ($row->values ?? '');
        // "Custom Amount" wali rows ka amount manual hota hai — number parse mat karo.
        $isCustom = stripos($valuesText, 'custom') !== false;
        $amount = ($isCustom || ! is_numeric($row->values ?? null)) ? 0.0 : (float) $row->values;
        $gstPct = (isset($row->gst_percentage) && is_numeric($row->gst_percentage))
            ? (float) $row->gst_percentage
            : 0.0;
        if (! $isCustom && ($amount <= 0 || $gstPct <= 0)) {
            // Text format ("Rs 50 + 18 % GST" / "$45") ho to parse karo.
            $parsed = self::parseDisputeValues($valuesText);
            if ($amount <= 0) {
                $amount = $parsed['amount'];
            }
            if ($gstPct <= 0) {
                $gstPct = $parsed['gst_pct'];
            }
        }
        if ($amount <= 0 && ! $skipFallback && ! $isCustom) {
            // DB row na mile to hardcoded fallback (same slab)
            [$amount, $gstPct] = match ($slab) {
                'slab_0_5' => [50.0, 18.0],
                'slab_5_20' => [100.0, 18.0],
                'slab_gt20' => [200.0, 18.0],
                default => [1000.0, 18.0],
            };
        }

        return [round($amount, 2), round($gstPct, 2)];
    }

    /**
     * Same chargeType ki SAARI DB rows lao (slab-wise pehli nahi).
     */
    protected static function findDisputeRows(string $chargeType): array
    {
        try {
            $variants = [];
            foreach ([$chargeType] as $v) {
                $v = trim((string) $v);
                if ($v === '') {
                    continue;
                }
                $variants[] = $v;
                $variants[] = str_replace(' ', '_', $v);
                $variants[] = str_replace('_', ' ', $v);
            }
            return DisputeCharge::whereIn('additional_charges', array_values(array_unique($variants)))
                ->where('status', 1)
                ->orderBy('id')
                ->get()
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Declared boxes normalize karo: [[L, W, H, weight], ...] (cm, kg).
     * Single box ['l'=>..,'w'=>..,'h'=>..] bhi chalega. Weight keys:
     * weight / chargeable_weight / actual_weight_kg.
     */
    protected static function normalizeBoxes($dims): array
    {
        if (! is_array($dims) || empty($dims)) {
            return [];
        }
        $list = $dims;
        if (isset($dims['l']) || isset($dims['length'])) {
            $list = [$dims];
        }
        $boxes = [];
        foreach ($list as $b) {
            if (! is_array($b)) {
                continue;
            }
            $l = (float) ($b['l'] ?? $b['length'] ?? 0);
            $w = (float) ($b['w'] ?? $b['width'] ?? 0);
            $h = (float) ($b['h'] ?? $b['height'] ?? 0);
            $wt = (float) ($b['weight'] ?? $b['chargeable_weight'] ?? $b['actual_weight_kg'] ?? 0);
            if ($l > 0 || $w > 0 || $h > 0 || $wt > 0) {
                $boxes[] = [$l, $w, $h, $wt];
            }
        }

        return $boxes;
    }

    /**
     * Girth formula (sheet ke hisaab se): girth = length + (width + height) * 2.
     */
    protected static function girthOf(float $l, float $w, float $h): float
    {
        return $l + 2 * ($w + $h);
    }

    /**
     * Boxes me se max length / width / girth.
     * @return array [maxL, maxW, maxGirth]
     */
    protected static function maxBoxDims(array $boxes): array
    {
        $maxL = 0.0;
        $maxW = 0.0;
        $maxG = 0.0;
        foreach ($boxes as [$l, $w, $h]) {
            if ($l > $maxL) {
                $maxL = $l;
            }
            if ($w > $maxW) {
                $maxW = $w;
            }
            $g = self::girthOf($l, $w, $h);
            if ($g > $maxG) {
                $maxG = $g;
            }
        }

        return [$maxL, $maxW, $maxG];
    }

    protected static function usaVariants(): array
    {
        return ['USA', 'US', 'UNITED STATES', 'UNITED STATES OF AMERICA' , 'US- United State of America'];
    }

    protected static function euUkCountries(): array
    {
        return [
            'UNITED KINGDOM', 'UK', 'GREAT BRITAIN', 'GB',
            'AUSTRIA', 'AT', 'BELGIUM', 'BE', 'BULGARIA', 'BG',
            'CROATIA', 'HR', 'CYPRUS', 'CY', 'CZECHIA', 'CZ',
            'CZECH REPUBLIC', 'DENMARK', 'DK', 'ESTONIA', 'EE',
            'FINLAND', 'FI', 'FRANCE', 'FR', 'GERMANY', 'DE',
            'GREECE', 'GR', 'HUNGARY', 'HU', 'IRELAND', 'IE',
            'ITALY', 'IT', 'LATVIA', 'LV', 'LITHUANIA', 'LT',
            'LUXEMBOURG', 'LU', 'MALTA', 'MT', 'NETHERLANDS', 'NL',
            'POLAND', 'PL', 'PORTUGAL', 'PT', 'ROMANIA', 'RO',
            'SLOVAKIA', 'SK', 'SLOVENIA', 'SI', 'SPAIN', 'ES',
            'SWEDEN', 'SE',
        ];
    }

    /**
     * Row destination vs input destination (input pehle se UPPER hai).
     * Admin modal list filtering me bhi reuse hota hai.
     */
    public static function matchDestination(?string $rowDest, string $destIn): bool
    {
        $rowDest = strtoupper(trim((string) $rowDest));
        if ($rowDest === '') {
            $rowDest = 'ALL';
        }
        if ($rowDest === 'ALL') {
            return true;
        }
        if ($rowDest === 'EU AND UK') {
            return in_array($destIn, self::euUkCountries(), true);
        }
        if ($rowDest === 'USA') {
            return in_array($destIn, self::usaVariants(), true);
        }

        return $rowDest === $destIn;
    }

    /**
     * Row service (UPS / DPD / ALL) vs input shipping method/service.
     * Admin modal list filtering me bhi reuse hota hai.
     */
    public static function matchService(?string $rowSvc, $service): bool
    {
        $rowSvc = strtolower(trim((string) $rowSvc));
        if ($rowSvc === '' || $rowSvc === 'all') {
            return true;
        }
        $service = strtolower(trim((string) $service));
        if ($service === '') {
            return false;
        }

        return str_contains($service, $rowSvc) || str_contains($rowSvc, $service);
    }

    /**
     * Matched row se standard result array banao.
     */
    protected static function matchedRow(string $chargeType, $row, string $slab, float $amount, float $gstPct): array
    {
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
    }

    /**
     * $noMatch closure jaisa array (helper methods ke liye static version).
     */
    protected static function noMatchResult(string $chargeType, string $reason): array
    {
        return [
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
    }

    /**
     * CSB-V Sucharges evaluation (single-case + first-scan master dono use karte hain).
     * Logic pehle jaisa hi hai — wt slab + customer type + destination + service.
     */
    protected static function evaluateCsbV(string $chargeType, float $wt, string $destIn, string $apiIn, string $calIn): array
    {
        // ---- STEP 1: condition check — wt slab + customer type ----
        $isCargo = ($apiIn === 'cargo');
        if (! $isCargo) {
            if ($wt <= 0) {
                return self::noMatchResult($chargeType, 'wt missing');
            }
            if ($wt >= 0.05 && $wt <= 5) {
                $slab = 'slab_0_5';
            } elseif ($wt > 5 && $wt <= 20) {
                $slab = 'slab_5_20';
            } elseif ($wt > 20) {
                $slab = 'slab_gt20';
            } else {
                return self::noMatchResult($chargeType, 'wt 0.05kg se kam: '.$wt.'kg');
            }
            // customer type check: sirf courier / ecommerce / ALL / empty pass
            if (! in_array($apiIn, ['', 'all', 'courier', 'ecommerce'], true)) {
                return self::noMatchResult($chargeType, 'customer type courier/ecommerce chahiye, mila: '.$apiIn);
            }
        } else {
            $slab = 'cargo';
        }

        // ---- STEP 2+3 ke liye DB row lao (values DB se, logic code se) ----
        // Strict: jo slab ka wt hai usi slab ki active row chahiye.
        // Row disabled/missing ho to charge mat dikhao (hardcoded fallback nahi).
        $row = self::findDisputeRow($chargeType, $slab);
        if (! $row) {
            return self::noMatchResult($chargeType, 'slab '.$slab.' ki active DB row nahi mili (disabled ya missing)');
        }

        // ---- STEP 2: destination check ----
        $rowDest = strtoupper(trim((string) ($row->destination ?? 'ALL')));
        if ($rowDest === '') {
            $rowDest = 'ALL';
        }
        if ($rowDest !== 'ALL' && $destIn !== 'ALL' && $rowDest !== $destIn) {
            return self::noMatchResult($chargeType, 'destination mismatch: row='.$rowDest.', input='.$destIn);
        }

        // ---- STEP 3: service_id + calculation_type check ----
        $rowSvc = strtolower(trim((string) ($row->service_id ?? 'all')));
        $isCustomerTypeWord = in_array($apiIn, ['', 'all', 'courier', 'ecommerce', 'cargo'], true);
        if ($rowSvc !== '' && $rowSvc !== 'all' && ! $isCustomerTypeWord && $rowSvc !== $apiIn) {
            return self::noMatchResult($chargeType, 'service mismatch: row='.$rowSvc.', input='.$apiIn);
        }
        $rowCal = strtolower(trim((string) ($row->calculation_type ?? '')));
        if ($calIn !== '' && $calIn !== 'all' && $rowCal !== ''
            && stripos($rowCal, $calIn) === false && stripos($calIn, $rowCal) === false) {
            return self::noMatchResult($chargeType, 'calculation_type mismatch: row='.$rowCal.', input='.$calIn);
        }

        // ---- STEP 4: amount = values column, gst = gst_percentage column ----
        // Strict: sirf DB values use karo, hardcoded slab fallback nahi.
        [$amount, $gstPct] = self::resolveRowAmounts($row, $slab, true);
        if ($amount <= 0) {
            return self::noMatchResult($chargeType, 'slab '.$slab.' ki row me valid amount nahi hai');
        }

        return self::matchedRow($chargeType, $row, $slab, $amount, $gstPct);
    }

    /**
     * Non-Conveyable: 'extra large boxes' me numeric threshold nahi hai, isliye
     * scan operator ke confirmation flag par match hota hai.
     * $actual = ['non_conveyable' => true].
     */
    protected static function evaluateNonConveyable(string $chargeType, string $destIn, $service, $actual): ?array
    {
        $flag = is_array($actual) ? ($actual['non_conveyable'] ?? false) : false;
        if (is_array($flag)) {
            $confirmed = ! empty($flag);
        } else {
            $confirmed = ($flag === true || $flag === 1 || $flag === '1' || strtolower((string) $flag) === 'yes');
        }
        if (! $confirmed) {
            return null;
        }

        foreach (self::findDisputeRows($chargeType) as $row) {
            if (! self::matchDestination($row->destination ?? 'ALL', $destIn)) {
                continue;
            }
            if (! self::matchService($row->service_id ?? 'ALL', $service)) {
                continue;
            }
            [$amount, $gstPct] = self::resolveRowAmounts($row, 'extra_large_boxes');

            return self::matchedRow($chargeType, $row, 'extra_large_boxes', $amount, $gstPct);
        }

        return null;
    }

    /**
     * flat/box row ko boxes par expand karo. Har entry = matchedRow + 'box'
     * (1-based box number, ya null jab box-wise data na ho).
     * - oversize: actual box dims > declared box dims (pairwise; extra actual box = hit)
     * - weight dispute: actual box wt > declared box wt (pairwise)
     * - non-conveyable: flag true = saare boxes, flag [2,3] = wahi boxes
     * Box-wise data na ho to single null-box entry (double-count nahi hoga).
     */
    protected static function expandFlatBoxCharge(string $key, $row, array $declBoxes, array $actBoxes, $actual): array
    {
        $entries = [];
        $slabs = [
            'oversize' => 'actual_gt_declared',
            'weight' => 'actual_gt_declared',
            'non-conveyable' => 'extra_large_boxes',
        ];
        $slab = $slabs[$key] ?? 'per_box';
        $isCustom = ($key === 'weight');

        $addEntry = function ($boxNo, $slab) use ($row, $isCustom) {
            [$amount, $gstPct] = self::resolveRowAmounts($row, $slab, $isCustom);
            $entry = self::matchedRow((string) ($row->additional_charges ?? ''), $row, $slab, $amount, $gstPct);
            $entry['box'] = $boxNo;

            return $entry;
        };

        if ($key === 'non-conveyable') {
            $flag = is_array($actual) ? ($actual['non_conveyable'] ?? false) : false;
            if (is_array($flag)) {
                foreach ($flag as $boxNo) {
                    $entries[] = $addEntry((int) $boxNo, $slab);
                }
            } elseif (empty($declBoxes)) {
                $entries[] = $addEntry(null, $slab);
            } else {
                foreach ($declBoxes as $i => $b) {
                    $entries[] = $addEntry($i + 1, $slab);
                }
            }

            return $entries;
        }

        if ($key === 'oversize') {
            if (empty($declBoxes) || empty($actBoxes)) {
                $entries[] = $addEntry(null, $slab);

                return $entries;
            }
            $n = max(count($declBoxes), count($actBoxes));
            for ($i = 0; $i < $n; $i++) {
                $d = $declBoxes[$i] ?? null;
                $a = $actBoxes[$i] ?? null;
                if ($a && ! $d) {
                    $entries[] = $addEntry($i + 1, $slab); // undeclared extra box
                } elseif ($a && $d && ($a[0] > $d[0] || $a[1] > $d[1] || $a[2] > $d[2])) {
                    $entries[] = $addEntry($i + 1, $slab);
                }
            }

            return $entries;
        }

        // weight dispute
        $declHasWt = false;
        foreach ($declBoxes as $b) {
            if (($b[3] ?? 0) > 0) {
                $declHasWt = true;
                break;
            }
        }
        $actHasWt = false;
        foreach ($actBoxes as $b) {
            if (($b[3] ?? 0) > 0) {
                $actHasWt = true;
                break;
            }
        }
        if (! $declHasWt || ! $actHasWt) {
            $entries[] = $addEntry(null, $slab);

            return $entries;
        }
        $n = max(count($declBoxes), count($actBoxes));
        for ($i = 0; $i < $n; $i++) {
            $dw = isset($declBoxes[$i]) ? (float) ($declBoxes[$i][3] ?? 0) : 0.0;
            $aw = isset($actBoxes[$i]) ? (float) ($actBoxes[$i][3] ?? 0) : 0.0;
            if ($aw > $dw && $aw > 0) {
                $entries[] = $addEntry($i + 1, $slab);
            }
        }

        return $entries;
    }

    /**
     * Master case — first scan par TABLE KE SAARE charge types evaluate karo. Har type ka pehla matching row collect hota hai.
     * @return array single-shape result + 'charges' list + 'row_ids' + summed totals
     */
    protected static function evaluateFirstScan(string $chargeType, float $wt, string $destIn, string $apiIn, string $calIn, $service, $dims, $actual): array
    {
        try {
            $types = DisputeCharge::distinct()
                ->where('status', 1)
                ->orderBy('additional_charges')
                ->pluck('additional_charges')
                ->all();
        } catch (\Throwable) {
            $types = [];
        }

        // Scan weight aaya ho to CSB-V slab usi par lagega.
        $actualWt = is_array($actual) ? (float) ($actual['weight'] ?? 0) : 0.0;
        $csbWt = $actualWt > 0 ? $actualWt : $wt;

        $all = [];
        foreach ($types as $type) {
            $key = strtolower(str_replace('_', ' ', trim((string) $type)));
            $res = match ($key) {
                'csb-v sucharges', 'csb-v surcharges' => self::evaluateCsbV($type, $csbWt, $destIn, $apiIn, $calIn),
                'additional handling surcharge' => self::evaluateHandlingCharge($type, $wt, $destIn, $service, $dims),
                'large packet surcharge' => self::evaluateLargePacket($type, $wt, $destIn, $service, $dims),
                'non-conveyable sucharges', 'non-conveyable surcharges' => self::evaluateNonConveyable($type, $destIn, $service, $actual),
                'oversize sucharges', 'oversize surcharges' => self::evaluateOversize($type, $dims, $actual, $destIn, $service),
                'weight dispute' => self::evaluateWeightDispute($type, $wt, $actual, $destIn, $service),
                default => null, // unknown types skip
            };
            if (! is_array($res) || empty($res['matched'])) {
                continue;
            }
            // flat/box rows box-wise expand hote hain (box_breakdown me jayenge),
            // flat rows shipment-level single entry rehte hain.
            $calc = strtolower((string) ($res['calculation_type'] ?? ''));
            $isFlatBox = str_contains($calc, 'flat') && str_contains($calc, 'box');
            if (! $isFlatBox) {
                $res['box'] = null;
                $all[] = $res;
                continue;
            }
            $row = DisputeCharge::find($res['row_id'] ?? 0);
            if (! $row) {
                $res['box'] = null;
                $all[] = $res;
                continue;
            }
            $flatKey = str_contains($key, 'oversize') ? 'oversize'
                : (str_contains($key, 'weight') ? 'weight' : 'non-conveyable');
            $declBoxes = self::normalizeBoxes($dims);
            $actBoxes = self::normalizeBoxes(is_array($actual) ? ($actual['boxes'] ?? []) : []);
            $expanded = self::expandFlatBoxCharge($flatKey, $row, $declBoxes, $actBoxes, $actual);
            if (empty($expanded)) {
                $res['box'] = null;
                $all[] = $res;
            } else {
                foreach ($expanded as $entry) {
                    $all[] = $entry;
                }
            }
        }

        // Box-wise grouping view.
        $boxBreakdown = [];
        foreach ($all as $entry) {
            $boxKey = $entry['box'] ?? null;
            $groupKey = $boxKey === null ? 'unassigned' : ('box_' . $boxKey);
            if (! isset($boxBreakdown[$groupKey])) {
                $boxBreakdown[$groupKey] = ['box' => $boxKey, 'charges' => [], 'total' => 0.0];
            }
            $boxBreakdown[$groupKey]['charges'][] = $entry;
            $boxBreakdown[$groupKey]['total'] = round($boxBreakdown[$groupKey]['total'] + (float) ($entry['total'] ?? 0), 2);
        }
        $boxBreakdown = array_values($boxBreakdown);

        $rowIds = array_values(array_filter(array_map(
            fn ($c) => $c['row_id'] ?? null,
            $all
        )));
        $amount = round(array_sum(array_map(fn ($c) => (float) ($c['amount'] ?? 0), $all)), 2);
        $gstAmt = round(array_sum(array_map(fn ($c) => (float) ($c['gst_amount'] ?? 0), $all)), 2);

        return [
            'matched' => ! empty($all),
            'charge_type' => $chargeType,
            'slab' => null,
            'amount' => $amount,
            'gst_pct' => 0.0,
            'gst_amount' => $gstAmt,
            'total' => round($amount + $gstAmt, 2),
            'calculation_type' => 'mixed',
            'place_of_apply' => 'weighing at first scan',
            'destination' => 'ALL',
            'row_id' => null,
            'row_ids' => $rowIds,
            'charges' => $all,
            'box_breakdown' => $boxBreakdown,
            'reason' => ! empty($all) ? 'ok' : 'koi first-scan charge match nahi hua',
        ];
    }

    /**
     * Additional Handling Surcharge: har DB row ki apni condition evaluate karo,
     * pehli matching row ka amount/GST do.
     */
    protected static function evaluateHandlingCharge(string $chargeType, float $wt, string $destIn, $service, $dims): ?array
    {
        $boxes = self::normalizeBoxes($dims);
        [$maxL, $maxW, $maxG] = self::maxBoxDims($boxes);

        foreach (self::findDisputeRows($chargeType) as $row) {
            if (! self::matchDestination($row->destination ?? 'ALL', $destIn)) {
                continue;
            }
            if (! self::matchService($row->service_id ?? 'ALL', $service)) {
                continue;
            }
            $cond = strtolower((string) ($row->conditions ?? ''));
            $hit = false;
            $slab = null;
            if (str_contains($cond, 'brown carton') || str_contains($cond, 'carton')) {
                continue; // packaging type ka data nahi hai — manual review
            } elseif (str_contains($cond, '266')) {
                // length + girth > 266 cm
                foreach ($boxes as [$l, $w, $h]) {
                    if ($l + self::girthOf($l, $w, $h) > 266) {
                        $hit = true;
                        break;
                    }
                }
                $slab = 'girth_gt_266';
            } elseif (str_contains($cond, '120')) {
                $hit = $maxL > 120;
                $slab = 'length_gt_120';
            } elseif (str_contains($cond, '75')) {
                $hit = $maxW > 75;
                $slab = 'width_gt_75';
            } elseif (str_contains($cond, '21')) {
                $hit = $wt > 21;
                $slab = 'wt_gt_21';
            } else {
                continue;
            }
            if ($hit) {
                [$amount, $gstPct] = self::resolveRowAmounts($row, $slab);

                return self::matchedRow($chargeType, $row, $slab, $amount, $gstPct);
            }
        }

        return null;
    }

    /**
     * Large Packet Surcharge: USA/UPS row (330/243) ya EU-UK/DPD row (30kg/175/300).
     */
    protected static function evaluateLargePacket(string $chargeType, float $wt, string $destIn, $service, $dims): ?array
    {
        $boxes = self::normalizeBoxes($dims);
        [$maxL, $maxW, $maxG] = self::maxBoxDims($boxes);

        foreach (self::findDisputeRows($chargeType) as $row) {
            if (! self::matchDestination($row->destination ?? 'ALL', $destIn)) {
                continue;
            }
            if (! self::matchService($row->service_id ?? 'ALL', $service)) {
                continue;
            }
            $cond = strtolower((string) ($row->conditions ?? ''));
            $hit = false;
            $slab = null;
            if (str_contains($cond, '330')) {
                // length + girth > 330 cm or length > 243 cm
                foreach ($boxes as [$l, $w, $h]) {
                    if ($l + self::girthOf($l, $w, $h) > 330 || $l > 243) {
                        $hit = true;
                        break;
                    }
                }
                $slab = 'packet_gt_330';
            } elseif (str_contains($cond, '175')) {
                // wt > 30 kg or length > 175 cm or girth > 300 cm
                $hit = $wt > 30 || $maxL > 175 || $maxG > 300;
                $slab = 'packet_dpd_limits';
            } else {
                continue;
            }
            if ($hit) {
                [$amount, $gstPct] = self::resolveRowAmounts($row, $slab);

                return self::matchedRow($chargeType, $row, $slab, $amount, $gstPct);
            }
        }

        return null;
    }

    /**
     * Oversize: actual dims > declared dims (scan data chahiye).
     * $actual = ['boxes' => [[l,w,h], ...]].
     */
    protected static function evaluateOversize(string $chargeType, $dims, $actual, string $destIn, $service): ?array
    {
        $decl = self::maxBoxDims(self::normalizeBoxes($dims));
        $actBoxes = self::normalizeBoxes(is_array($actual) ? ($actual['boxes'] ?? []) : []);
        if (empty($actBoxes)) {
            return null;
        }
        $act = self::maxBoxDims($actBoxes);
        if (! ($act[0] > $decl[0] || $act[1] > $decl[1] || $act[2] > $decl[2])) {
            return null;
        }

        foreach (self::findDisputeRows($chargeType) as $row) {
            if (! self::matchDestination($row->destination ?? 'ALL', $destIn)) {
                continue;
            }
            if (! self::matchService($row->service_id ?? 'ALL', $service)) {
                continue;
            }
            [$amount, $gstPct] = self::resolveRowAmounts($row, 'actual_gt_declared');

            return self::matchedRow($chargeType, $row, 'actual_gt_declared', $amount, $gstPct);
        }

        return null;
    }

    /**
     * Weight dispute: actual wt > declared wt. Amount custom (manual) hai,
     * isliye amount 0 ke saath match return hota hai.
     * $actual = ['weight' => ...].
     */
    protected static function evaluateWeightDispute(string $chargeType, float $wt, $actual, string $destIn, $service): ?array
    {
        $actualWt = is_array($actual) ? (float) ($actual['weight'] ?? 0) : 0.0;
        if ($actualWt <= 0 || ! ($actualWt > $wt)) {
            return null;
        }

        foreach (self::findDisputeRows($chargeType) as $row) {
            if (! self::matchDestination($row->destination ?? 'ALL', $destIn)) {
                continue;
            }
            if (! self::matchService($row->service_id ?? 'ALL', $service)) {
                continue;
            }
            [$amount, $gstPct] = self::resolveRowAmounts($row, 'actual_gt_declared', true);

            return self::matchedRow($chargeType, $row, 'actual_gt_declared', $amount, $gstPct);
        }

        return null;
    }

    /**
     * DDP Sucharges: applies when tariff type DDP is selected at shipment
     * creation. Incoterms arrive via $actual['incoterms'] (creation flow).
     * Testing rate is a flat $20 + 18% GST from the DB row.
     */
    protected static function evaluateDdp(string $chargeType, string $destIn, $service, $actual): ?array
    {
        $incoterms = strtoupper(trim((string) (is_array($actual) ? ($actual['incoterms'] ?? '') : '')));
        if ($incoterms !== 'DDP') {
            return null;
        }

        foreach (self::findDisputeRows($chargeType) as $row) {
            if (! self::matchDestination($row->destination ?? 'ALL', $destIn)) {
                continue;
            }
            if (! self::matchService($row->service_id ?? 'ALL', $service)) {
                continue;
            }
            [$amount, $gstPct] = self::resolveRowAmounts($row, 'flat');

            return self::matchedRow($chargeType, $row, 'ddp', $amount, $gstPct);
        }

        return null;
    }

    /**
     * Go Green Plus Charges: DHL service only, Rs 30 per kg on
     * ceil(chargeable weight) + rule GST.
     */
    protected static function evaluateGoGreen(string $chargeType, float $wt, string $destIn, $service): ?array
    {
        if ($wt <= 0) {
            return null;
        }

        foreach (self::findDisputeRows($chargeType) as $row) {
            if (! self::matchDestination($row->destination ?? 'ALL', $destIn)) {
                continue;
            }
            if (! self::matchService($row->service_id ?? 'ALL', $service)) {
                continue;
            }
            [$rate, $gstPct] = self::resolveRowAmounts($row, 'flat');
            $amount = round($rate * (float) ceil($wt), 2);

            return self::matchedRow($chargeType, $row, 'per_kg', $amount, $gstPct);
        }

        return null;
    }
}
