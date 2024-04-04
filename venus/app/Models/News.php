<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    protected $table='news';
    protected $fillable=[
        'user_id',
        'title',
        'description',
        'image',
        'date',
        'time',
        'status'

    ];
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
