<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Votes extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table='votes';
    protected $fillable=[
        'user_id',
        'property_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'polling_option',
        'date',
        'time',
        'status'
    ];
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
