<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Total_builder_dividend extends Model
{
    use HasFactory;
   protected $table='builder_dividends_total';
   protected $fillable=[
   'builder_id',
   'property_id',
   'total_amount',
   'status'
   ]; 
}
