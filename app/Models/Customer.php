<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'village_name',
        'reminder_date'
    ];
    
    protected function paymentPending(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->orderDetails->sum('price') - $this->payments->sum('amount_paid')) ?? 0,
        );
    }

    public function orderDetails()
    {
        return $this->hasManyThrough(OrderDetail::class , Order::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
