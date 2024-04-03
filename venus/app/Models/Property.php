<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $table='poperties';
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
        'cap',
        'annual_recurring_avenue',
        'dividend',
        'declaration',
        'buider_wallet_address',
        'min_amount',
        'max_amount',
        'start_date',
        'end_date',
        'status'

    ];
}
