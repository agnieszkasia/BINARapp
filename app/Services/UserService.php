<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function create($data)
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

        $user->save();
    }

    public function validate($request)
    {
        $request->validate([
            'companyName' => ['required', 'string', 'max:255', 'min:2'],
            'firstName' => ['required','string', 'max:255', 'min:2'],
            'lastName' => ['required', 'string', 'max:255', 'min:2'],
            'address' => ['required', 'string', 'max:255', 'min:2'],
            'birthDate' => ['required', 'string','regex:/19[0-9]{2}|200[0,1,2,3]-[0-9]{2}-[0-9]{2}/u', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phoneNumber' => ['required', 'string','regex:/^[0-9]{9}/u', 'size:9'],
            'nip' => ['required', 'string', 'regex:/[0-9]{10}/u', 'size:10'],
            'taxOfficeCode' => ['required', 'string'],
            'file' => ['required'],
        ]);
    }
}
