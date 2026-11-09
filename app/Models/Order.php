<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_code',
        'user_id',
        'coupon_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'note',
        'subtotal',
        'discount_amount',
        'shipping_fee',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'cancelled_reason',
        'inventory_settled',
        'restocked_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'inventory_settled' => 'boolean',
            'restocked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function isPending(): bool
    {
        return $this->order_status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->order_status === 'confirmed';
    }

    public function isProcessing(): bool
    {
        return $this->order_status === 'processing';
    }

    public function isShipping(): bool
    {
        return $this->order_status === 'shipping';
    }

    public function isDelivered(): bool
    {
        return $this->order_status === 'delivered';
    }

    public function isCancelled(): bool
    {
        return $this->order_status === 'cancelled';
    }

    public function isFailedDelivery(): bool
    {
        return $this->order_status === 'failed_delivery';
    }

    public function canBeCancelled(): bool
    {
        return ! in_array($this->order_status, ['delivered', 'cancelled']);
    }

    public function canBeCancelledByCustomer(): bool
    {
        return in_array($this->order_status, ['pending', 'confirmed']);
    }

    public function canBeRestocked(): bool
    {
        return ! $this->inventory_settled && in_array($this->order_status, ['cancelled', 'failed_delivery']);
    }
}
