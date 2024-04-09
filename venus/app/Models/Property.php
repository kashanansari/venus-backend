<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Property extends Model
{
    use SoftDeletes;

    use HasFactory;
    protected $table='properties';
    protected $fillable=[
        'user_id',
        'images',
        'property_name',
        'property_type',
        'property_size',
        'rental_price',
        'rental_frequency',
        'no_of_bedrooms',
        'amenities',
        'description',
        'verification_details',
        'property_address',
        'project_completion_date',
        'floor',
        'govt_assessed_land',
        'attachment',
        'cap',
        'annual_recurring_revenue',
        'dividend',
        'declaration',
        'buider_wallet_address',
        'min_amount',
        'max_amount',
        'start_date',
        'end_date',
        'status',
        'total_raised_amount',
        'floor_area',
        'zoning',
        'gross'

    ];
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
