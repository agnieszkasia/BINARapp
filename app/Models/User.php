<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
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

    public function create($data)
    {

    }
}
