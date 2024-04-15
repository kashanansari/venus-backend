<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_dividend extends Model
{   
    use HasFactory;
    protected $table="user_dividends";
    protected $fillable=[
    'user_id',
    'property_id',
    'amount',
    'date',
    'time',
    'status'
    ];
}
