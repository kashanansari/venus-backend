<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Total_user_dividend extends Model
{
    use HasFactory;
    protected $table='user_dividends_total';
    protected $fillable=[
        'user_id',
        'property_id',
        'total_amount',
        'status'
        ]; 
}
