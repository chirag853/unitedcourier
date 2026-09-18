<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeliveryAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly int $shipmentInvoiceId,
        private readonly ?int $shipperId,
        private readonly ?string $awbNumber,
        private readonly ?string $invoiceNumber,
        private readonly ?string $shipperCompany,
        private readonly ?string $destination,
        private readonly ?string $assignedBy,
        private readonly ?string $manifestNumber = null,
        private readonly ?int $shipmentCount = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $reference = $this->awbNumber ?: ($this->invoiceNumber ?: '#' . $this->shipmentInvoiceId);

        // Bulk (manifest-level) assignment: one consolidated message.
        if ($this->manifestNumber && $this->shipmentCount) {
            $message = 'Manifest ' . $this->manifestNumber . ' (' . $this->shipmentCount . ' shipments) has been assigned to you for pickup.';
        } else {
            $message = 'A new delivery (' . $reference . ') has been assigned to you.';
        }

        return [
            'kind' => 'delivery_assigned',
            'title' => 'New Delivery Assigned',
            'message' => $message,
            'shipment_invoice_id' => $this->shipmentInvoiceId,
            'shipper_id' => $this->shipperId,
            'awb_number' => $this->awbNumber,
            'invoice_number' => $this->invoiceNumber,
            'shipper_company' => $this->shipperCompany,
            'destination' => $this->destination,
            'assigned_by' => $this->assignedBy,
            'manifest_number' => $this->manifestNumber,
            'shipment_count' => $this->shipmentCount,
            'url' => route('admin.delivery-dashboard'),
        ];
    }
}
