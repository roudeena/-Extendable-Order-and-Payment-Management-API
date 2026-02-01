<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'payment_method',
        'transaction_reference',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
