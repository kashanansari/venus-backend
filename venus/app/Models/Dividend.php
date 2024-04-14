<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dividend extends Model
{
    use HasFactory;
    protected $table="dividends";
    protected $fillable=[
    'user_id',
    'property_id',
    'amount',
    'date',
    'time',
    'status'
    ];
}
