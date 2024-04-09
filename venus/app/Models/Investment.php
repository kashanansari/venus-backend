<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Investment extends Model
{
    use SoftDeletes;

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
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
