<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'companyName' => ['required', 'string', 'max:255', 'min:2'],
            'firstName' => ['required','string', 'max:255', 'min:2'],
            'lastName' => ['required', 'string', 'max:255', 'min:2'],
            'address' => ['required', 'string', 'max:255', 'min:2'],
            'birthDate' => ['required', 'string','regex:/19[0-9]{2}|200[0,1,2,3]-[0-9]{2}-[0-9]{2}/u', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phoneNumber' => ['required', 'string','regex:/^[0-9]{9}/u', 'size:9'],
            'nip' => ['required', 'string', 'regex:/[0-9]{10}/u', 'size:10'],
            'taxOfficeCode' => ['required', 'string'],
        ];
    }
}
