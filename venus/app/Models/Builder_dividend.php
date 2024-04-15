<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Builder_dividend extends Model
{
    use HasFactory;
    protected $tbale='builder_dividends';
    protected $fillable=[
    'builder_id',
    'property_id',
    'amount',
    'date',
    'time',
    'status'
    ];
}
