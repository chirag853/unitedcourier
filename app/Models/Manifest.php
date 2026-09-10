<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manifest extends Model
{
    /**
     * Manifest status constants.
     * 0 = open, 1 = remove, 3 = close, 4 = pickup
     */
    public const STATUS_OPEN    = 0;
    public const STATUS_REMOVE  = 1;
    public const STATUS_CLOSE   = 3;
    public const STATUS_PICKUP  = 4;

    /**
     * Human readable labels for each manifest status.
     *
     * @var array<int, string>
     */
    public const STATUS_LABELS = [
        self::STATUS_OPEN    => 'Open',
        self::STATUS_REMOVE  => 'Remove',
        self::STATUS_CLOSE   => 'Close',
        self::STATUS_PICKUP  => 'Pickup',
    ];

    /**
     * Bootstrap badge class used to render each manifest status.
     *
     * @var array<int, string>
     */
    public const STATUS_BADGES = [
        self::STATUS_OPEN    => 'bg-info',
        self::STATUS_REMOVE  => 'bg-danger',
        self::STATUS_CLOSE   => 'bg-dark',
        self::STATUS_PICKUP  => 'bg-warning text-dark',
    ];

    /**
     * Resolve the human readable label for a manifest status value.
     */
    public static function statusLabel(int $status): string
    {
        return self::STATUS_LABELS[$status] ?? 'Unknown';
    }

    /**
     * Resolve the bootstrap badge class for a manifest status value.
     */
    public static function statusBadgeClass(int $status): string
    {
        return self::STATUS_BADGES[$status] ?? 'bg-secondary';
    }

    /**
     * Accessor for the current record's status label (e.g. $manifest->status_label).
     */
    public function getStatusLabelAttribute(): string
    {
        return self::statusLabel((int) $this->status);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'manifest_number',
        'shipper_id',
        'customer_id',
        'status',
        'pickup_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'shipper_id'  => 'integer',
        'customer_id' => 'integer',
        'status'      => 'integer',
        'pickup_date' => 'date:Y-m-d',
    ];

    /**
     * Get the shipper that owns the manifest.
     */
    public function shipper()
    {
        return $this->belongsTo(ShipperInfo::class, 'shipper_id');
    }

    /**
     * Get the customer that owns the manifest.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Generate the next manifest number in the format MUWCYYMMDDNNNNN.
     *
     * Sequence (NNNNN) is 5 digits, zero-padded, unique per day.
     * Example: MUWC26090800001
     *
     * @return string
     */
    public static function generateManifestNumber(): string
    {
        $prefix = 'MUWC' . now()->format('ymd');
        $last = self::where('manifest_number', 'like', $prefix . '%')
            ->orderBy('manifest_number', 'desc')
            ->value('manifest_number');

        $next = 1;
        if ($last) {
            $lastSequence = (int) substr($last, -5);
            $next = $lastSequence + 1;
        }

        return $prefix . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Create a manifest record for a successfully manifested shipment.
     *
     * When $manifestNumber is provided (bulk manifest flow) the same number
     * is shared by every shipment in the batch; otherwise a fresh unique
     * number is generated per shipment (single manifest flow).
     *
     * @param int      $shipperId
     * @param int      $customerId
     * @param string|null $manifestNumber
     * @return self
     */
    public static function createForShipper(int $shipperId, int $customerId, ?string $manifestNumber = null): self
    {
        return self::create([
            'manifest_number' => $manifestNumber ?? self::generateManifestNumber(),
            'shipper_id'      => $shipperId,
            'customer_id'     => $customerId,
            'status'          => self::STATUS_OPEN,
        ]);
    }
}
