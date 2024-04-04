<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVotes extends Model
{
    use HasFactory;
    protected $table='user_votes';
    protected $fillable=[
        'user_id',
        'vote_id',
        'admin_id',
        'vote_choice',
        'date',
        'time',
        'status'
    ];
}
