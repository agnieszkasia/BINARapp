<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function addFromForm($data)
    {
        $user = new User();

        $user->setAttribute('company_name', $data['companyName']);
        $user->setAttribute('first_name', $data['firstname']);
        $user->setAttribute('last_name', $data['lastname']);
        $user->setAttribute('address', $data['address']);
        $user->setAttribute('birth_date', $data['birthDate']);
        $user->setAttribute('email', $data['email']);
        $user->setAttribute('nip', $data['nip']);
        $user->setAttribute('phone_number', $data['phoneNumber']);
        $user->setAttribute('tax_office_code', substr($data['taxOfficeCode'],0,4));

        $user->save();
    }
}
