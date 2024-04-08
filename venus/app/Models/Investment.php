<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;
    protected $table="investments";
    protected $fillable=[
        'user_id',
        'property_id',
        'wallet_address',
        'invested_amount',
        'invested_date',
        'invested_time',
    ];
}
