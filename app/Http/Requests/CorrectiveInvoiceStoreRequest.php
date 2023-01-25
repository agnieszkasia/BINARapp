<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorrectiveInvoiceStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'issue_date' => ['required', 'string', 'regex:/^(([0-2]{0,1}[0-9]{1})|(3[01]))\.[0-9]{2}\.(20[0-9]{2})$/u'],
            'due_date' => ['required', 'string', 'regex:/^(([0-2]{0,1}[0-9]{1})|(3[01]))\.[0-9]{2}\.(20[0-9]{2})$/u'],
            'invoice_number' => ['required', 'string', 'max:255', 'min:2'],
            'nip' => ['required', 'string', 'regex:/[0-9]{10}/u', 'size:10'],
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'address' => ['required', 'string', 'max:255', 'min:2'],
            'gross' => ['required','regex:/^\d*(\.\d{1,2})?$/', 'max:255'],
            'net' => ['required','regex:/^\d*(\.\d{1,2})?$/', 'max:255'],
            'vat' => ['required','regex:/^\d*(\.\d{1,2})?$/', 'max:255'],
        ];
    }
}
