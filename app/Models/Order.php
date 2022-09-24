<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'payment_mode'
    ];


    protected function orderTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->orderDetails->sum('price'),
        );
    }

    protected function orderDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($this->created_at)->format('M d, Y'),
        );
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
