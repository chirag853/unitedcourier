<?php

namespace App\Services;

use App\Models\CourierService;
use App\Models\CreateShipment;
use App\Models\PackageDimension;
use App\Models\ShipmentInvoice;
use App\Models\ShipmentInvoiceItem;
use App\Models\ShipperInfo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Picqer\Barcode\BarcodeGeneratorPNG;

/**
 * Builds the same shipping label that the customer "Print Label" modal
 * (view-all-shipments #printLabelBody) produces for Ready shipments,
 * but server-side so API manifests get an identical custom_label PDF.
 */
class CustomerLabelService
{
    /**
     * Generate + store the Ready-style custom label PDF when missing.
     * Mirrors mark-packed: stores the PDF URL in shipper.custom_label and
     * moves ready -> packed. Returns the label URL (or null on failure).
     */
    public function ensureReadyLabel(ShipperInfo $shipper): ?string
    {
        $shipper->loadMissing([
            'consigneeInfo',
            'packageDimensions',
            'csbInformation',
        ]);

        if (! empty($shipper->custom_label)) {
            return $shipper->custom_label;
        }

        if (! in_array($shipper->status, ['ready', 'packed'], true)) {
            return null;
        }

        try {
            $html = $this->buildLabelHtml($shipper);
            [$path, $url] = $this->storeLabelPdf($shipper, $html);

            $shipper->custom_label = $url;
            if ($shipper->status === 'ready') {
                $shipper->status = 'packed';
            }
            $shipper->save();

            return $url;
        } catch (\Throwable $e) {
            \Log::warning('API custom label generation skipped.', [
                'shipper_id' => $shipper->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Label body mirroring the Print Label modal (#printLabelBody):
     * logo + AWB barcode, SHIP FROM / SHIP TO, SHIPMENT INFO,
     * INVOICE ITEMS, PACKAGE DIMENSIONS.
     */
    public function buildLabelHtml(ShipperInfo $shipper): string
    {
        $e = fn ($v) => htmlspecialchars((string) ($v ?? '-'), ENT_QUOTES, 'UTF-8');
        $consignee = $shipper->consigneeInfo;

        $invoice = ShipmentInvoice::where('shipper_id', $shipper->id)->first();
        $items = $invoice
            ? ShipmentInvoiceItem::where('invoice_id', $invoice->id)->orderBy('box_no')->get()
            : collect();
        $packages = $shipper->packageDimensions instanceof \Traversable
            ? collect($shipper->packageDimensions)
            : $shipper->packageDimensions;

        $awb = (string) ($shipper->awb_number ?: $shipper->id);
        $barcode = $this->barcodeHtml($awb);

        $logoPath = public_path('assets/img/logo.png');
        $logo = is_file($logoPath)
            ? '<img src="' . $logoPath . '" alt="United Courier" style="max-height:65px;">'
            : '<strong>United Courier</strong>';

        $shipAddr = trim(implode(', ', array_filter([
            $shipper->address_line1, $shipper->address_line2, $shipper->address_line3,
        ])));
        $shipCityPin = trim(implode(', ', array_filter([
            $shipper->city, $shipper->state, $shipper->pincode,
        ])));

        $conAddr = $conCityZip = $conName = $conContact = $conPhone = '-';
        if ($consignee) {
            $conName = $consignee->consignee_name;
            $conContact = $consignee->contact_person;
            $conAddr = trim(implode(', ', array_filter([
                $consignee->address_line1, $consignee->address_line2, $consignee->address_line3,
            ])));
            $conCityZip = trim(implode(', ', array_filter([
                $consignee->city, $consignee->state, $consignee->zip_code,
            ])));
            $conPhone = $consignee->phone_number;
        }

        $serviceCode = $this->resolveServiceCode($shipper);
        $invoiceDate = $invoice && $invoice->invoice_date
            ? $invoice->invoice_date->format('d-m-Y')
            : '-';

        $itemsRows = '';
        $itemsTotal = 0;
        foreach ($items as $item) {
            $amount = (float) ($item->amount ?? ((float) $item->qty * (float) $item->unit_rate));
            $itemsTotal += $amount;
            $itemsRows .= '<tr>'
                . '<td>' . $e($item->box_no) . '</td>'
                . '<td>' . $e($item->description) . '</td>'
                . '<td>' . $e($item->hs_code) . '</td>'
                . '<td>' . $e($item->qty) . '</td>'
                . '<td>' . $e($item->unit_rate) . '</td>'
                . '<td>' . $e($item->igst_percentage) . '</td>'
                . '<td>' . $e($item->igst_amount) . '</td>'
                . '<td>' . $e(number_format($amount, 2)) . '</td>'
                . '</tr>';
        }

        $pkgCards = '';
        $idx = 0;
        foreach ($packages as $pkg) {
            $idx++;
            $pkgCards .= '<div style="border:1px solid #dee2e6;border-radius:6px;padding:8px;margin-bottom:6px;font-size:12px;">'
                . '<strong>Box #' . $idx . '</strong>: '
                . 'L: ' . $e($pkg->length_cm) . ' x W: ' . $e($pkg->width_cm) . ' x H: ' . $e($pkg->height_cm) . ' cm | '
                . 'Billable Wt: ' . $e($pkg->chargeable_weight) . ' Kg'
                . '</div>';
        }

        $body = ''
            . '<table style="width:100%;margin-bottom:12px;" cellpadding="0" cellspacing="0"><tr>'
            . '<td style="width:35%;vertical-align:middle;">' . $logo . '</td>'
            . '<td style="width:65%;vertical-align:middle;">' . $barcode . '</td>'
            . '</tr></table>'
            . '<hr>'
            . '<table style="width:100%;margin-bottom:8px;" cellpadding="0" cellspacing="0"><tr>'
            . '<td style="width:50%;vertical-align:top;">'
            . '<strong style="font-size:13px;">SHIP FROM</strong>'
            . '<p style="font-size:13px;">' . $e($shipper->company_name) . '</p>'
            . '<p style="font-size:12px;">' . $e($shipper->contact_person) . '</p>'
            . '<p style="font-size:12px;">' . $e($shipAddr) . '</p>'
            . '<p style="font-size:12px;">' . $e($shipCityPin) . '</p>'
            . '<p style="font-size:12px;">Phone: ' . $e($shipper->phone_number) . '</p></td>'
            . '<td style="width:50%;vertical-align:top;">'
            . '<strong style="font-size:13px;">SHIP TO</strong>'
            . '<p style="font-size:13px;">' . $e($conName) . '</p>'
            . '<p style="font-size:12px;">' . $e($conContact) . '</p>'
            . '<p style="font-size:12px;">' . $e($conAddr) . '</p>'
            . '<p style="font-size:12px;">' . $e($conCityZip) . '</p>'
            . '<p style="font-size:12px;">Phone: ' . $e($conPhone) . '</p></td>'
            . '</tr></table>'
            . '<hr>'
            . '<div style="font-size:12px;"><strong style="font-size:13px;">SHIPMENT INFO</strong>'
            . '<p><strong>Invoice No.:</strong> ' . $e($invoice->invoice_number ?? null) . '</p>'
            . '<p><strong>Invoice Date:</strong> ' . $e($invoiceDate) . '</p>'
            . '<p><strong>Reference No.:</strong> ' . $e($invoice->reference_number ?? null) . '</p>'
            . '<p><strong>Method:</strong> ' . $e($shipper->shipping_method) . '</p>'
            . '<p><strong>Service Code:</strong> ' . $e($serviceCode) . '</p></div>'
            . '<hr>'
            . '<div><strong style="font-size:13px;">INVOICE ITEMS</strong>'
            . '<table border="1" cellpadding="4" cellspacing="0" style="width:100%;font-size:11px;border-collapse:collapse;">'
            . '<thead><tr><th>Box</th><th>Description</th><th>HS Code</th><th>Qty</th>'
            . '<th>Rate</th><th>IGST(%)</th><th>IGST</th><th>Amount</th></tr></thead>'
            . '<tbody>' . $itemsRows . '</tbody></table>'
            . '<p style="text-align:right;font-size:13px;"><strong>Total: ' . number_format($itemsTotal, 2) . '</strong></p></div>'
            . '<hr>'
            . '<div><strong style="font-size:13px;">PACKAGE DIMENSIONS</strong>' . $pkgCards . '</div>';

        $awbSafe = htmlspecialchars($awb, ENT_QUOTES, 'UTF-8');

        return '<!DOCTYPE html>' . PHP_EOL
            . '<html lang="en"><head><meta charset="UTF-8">'
            . '<title>Shipping Label ' . $awbSafe . '</title>'
            . '<style>html,body{margin:0;padding:0;background:#fff;color:#000}'
            . 'body{font-family:Arial,sans-serif}.custom-label-document{box-sizing:border-box;width:100%}</style>'
            . '</head><body><div class="custom-label-document">' . $body . '</div></body></html>';
    }

    /**
     * CODE128 barcode (same symbology as the modal's JsBarcode) rendered as
     * a single PNG image with the AWB printed underneath — one barcode only.
     */
    private function barcodeHtml(string $awb): string
    {
        $awbSafe = htmlspecialchars($awb, ENT_QUOTES, 'UTF-8');

        try {
            $pngGen = new BarcodeGeneratorPNG();
            $png = $pngGen->getBarcode($awb, $pngGen::TYPE_CODE_128, 2, 60);
            $img = '<img src="data:image/png;base64,' . base64_encode($png) . '" style="max-width:300px;" alt="' . $awbSafe . '">';
        } catch (\Throwable $e) {
            $img = '';
        }

        return '<div style="text-align:right;">' . $img
            . '<div style="font-size:14px;letter-spacing:2px;font-weight:bold;">' . $awbSafe . '</div></div>';
    }

    private function resolveServiceCode(ShipperInfo $shipper): ?string
    {
        if (! empty($shipper->service_id)) {
            $code = CourierService::where('id', $shipper->service_id)->value('service_code');
            if ($code) {
                return $code;
            }
        }

        $method = CreateShipment::where('shipper_id', $shipper->id)->value('shipping_method')
            ?: $shipper->shipping_method;

        if ($method) {
            return CourierService::where('method', $method)->value('service_code');
        }

        return null;
    }

    /** @return array{0: string, 1: string} */
    private function storeLabelPdf(ShipperInfo $shipper, string $document): array
    {
        $name = Str::slug((string) ($shipper->awb_number ?: 'shipment-' . $shipper->id));
        $timestamp = now('Asia/Kolkata')->format('Ymd-His');
        $publicDirectory = public_path('uploads/custom_labels');

        if (! is_dir($publicDirectory) && ! mkdir($publicDirectory, 0775, true) && ! is_dir($publicDirectory)) {
            throw new \RuntimeException('Unable to create the public custom label directory.');
        }

        $pdfBytes = Pdf::loadHTML($document)->output();
        if (! is_string($pdfBytes) || $pdfBytes === '') {
            throw new \RuntimeException('The custom label PDF was not generated correctly.');
        }

        // Exclusive-create like the web flow; retry with a suffix on collision.
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $suffix = $attempt === 0 ? '' : '-' . $attempt;
            $filename = $name . '-' . $timestamp . $suffix . '.pdf';
            $publicPath = $publicDirectory . DIRECTORY_SEPARATOR . $filename;
            $destination = @fopen($publicPath, 'xb');
            if ($destination === false) {
                continue;
            }
            fwrite($destination, $pdfBytes);
            fclose($destination);

            return [$publicPath, asset('uploads/custom_labels/' . $filename)];
        }

        throw new \RuntimeException('Unable to create the custom label PDF in the public directory.');
    }
}
