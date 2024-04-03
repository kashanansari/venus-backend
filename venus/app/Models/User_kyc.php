<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_kyc extends Model
{
    use HasFactory;
    protected $table= 'user_kycs';

        

    protected $fillable = [
        'user_id',
        'date_of_birth',
        'cnic', // Ensure this matches the column name in the table
        'license',
        'passport',
        'yearly_tax_report',
        'nationality',
        'res_address',
        'street_address',
        'city',
        'state',
        'postal_code',
        'country',
        'additional_info',
        'occupation',
        'source_of_funds',
        'status',
        'date',
        'time',
        
    ];

}
