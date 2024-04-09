<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class News extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table='news';
    protected $fillable=[
        'user_id',
        'title',
        'description',
        'image',
        'date',
        'time',
        'status',
        'total_views'

    ];
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
