<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News_views extends Model
{
    use HasFactory;
    protected $table='news_views';
    protected $fillable=[
    'news_id',
    'user_id'
    ];
}
