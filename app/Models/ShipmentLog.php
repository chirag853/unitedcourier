<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ShipmentLog extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shipper_id',
        'customer_id',
        'awb_number',
        'status',
        'previous_status',
        'title',
        'description',
        'performed_by',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the shipper that owns the log.
     */
    public function shipper()
    {
        return $this->belongsTo(ShipperInfo::class, 'shipper_id');
    }

    /**
     * Get the customer that owns the log.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Create a shipment log entry for a status change.
     *
     * @param int    $shipperId
     * @param string $awbNumber
     * @param string $status
     * @param string|null $previousStatus
     * @param string|null $description
     * @param int|null $customerId
     * @param string $performedBy
     * @return self
     */
    public static function logStatus(
        int $shipperId,
        string $awbNumber,
        string $status,
        ?string $previousStatus = null,
        ?string $description = null,
        ?int $customerId = null,
        string $performedBy = 'customer'
    ): self {
        return self::create([
            'shipper_id'      => $shipperId,
            'customer_id'     => $customerId ?? self::resolveCustomerId(),
            'awb_number'      => $awbNumber,
            'status'          => $status,
            'previous_status' => $previousStatus,
            'title'           => Tracking::getTitleForStatus($status),
            'description'     => $description,
            'performed_by'    => $performedBy,
            'created_at'      => now(),
        ]);
    }

    /**
     * Resolve the authenticated customer ID from the guard.
     *
     * @return int|null
     */
    private static function resolveCustomerId(): ?int
    {
        $customer = Auth::guard('customer')->user();
        return $customer ? $customer->id : null;
    }
}
