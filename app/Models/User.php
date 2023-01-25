<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'password',
        'company_name',
        'first_name',
        'last_name',
        'address',
        'birthDate',
        'email',
        'nip',
        'phone_number',
        'tax_office_code'
    ];
}
