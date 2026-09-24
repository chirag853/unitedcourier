<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ManifestPickupAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $manifestNumber,
        private readonly int $shipmentCount,
        private readonly ?string $customerName,
        private readonly ?string $pickupDate = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $message = 'Manifest ' . $this->manifestNumber
            . ' (' . $this->shipmentCount . ' shipment' . ($this->shipmentCount === 1 ? '' : 's') . ')'
            . ' has been assigned for pickup'
            . ($this->customerName ? ' by ' . $this->customerName : '')
            . ($this->pickupDate ? ' (pickup date: ' . $this->pickupDate . ')' : '')
            . '.';

        return [
            'kind' => 'manifest_pickup_assigned',
            'title' => 'Manifest Assigned for Pickup',
            'message' => $message,
            'manifest_number' => $this->manifestNumber,
            'shipment_count' => $this->shipmentCount,
            'customer_name' => $this->customerName,
            'pickup_date' => $this->pickupDate,
            'url' => route('admin.manifest-detail', ['manifestNumber' => $this->manifestNumber]),
        ];
    }
}
