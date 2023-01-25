<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public static function createNewUser($data)
    {
        $user = new User();

        $user->setAttribute('company_name', $data['companyName']);
        $user->setAttribute('first_name', $data['firstName']);
        $user->setAttribute('last_name', $data['lastName']);
        $user->setAttribute('address', $data['address']);
        $user->setAttribute('birth_date', $data['birthDate']);
        $user->setAttribute('email', $data['email']);
        $user->setAttribute('nip', $data['nip']);
        $user->setAttribute('phone_number', $data['phoneNumber']);
        $user->setAttribute('tax_office_code', substr($data['taxOfficeCode'],0,4));
        $user->setAttribute('password', Hash::make($data['password']));

        $user->save();
    }
}
