<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseInvoiceStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'issue_date' => ['required', 'date', 'date_multi_format:"Y-m-d"'],
            'due_date' => ['required', 'date', 'date_multi_format:"Y-m-d"'],
            'invoice_number' => ['required', 'string', 'max:255', 'min:2'],
            'nip' => ['required', 'string', 'regex:/[0-9]{10}/u', 'size:10'],
            'company' => ['required', 'string', 'max:255', 'min:2'],
            'address' => ['required', 'string', 'max:255', 'min:2'],
            'gross' => ['required','regex:/^\d*(\.\d{1,2})?$/', 'max:255'],
            'net' => ['required','regex:/^\d*(\.\d{1,2})?$/', 'max:255'],
            'vat' => ['required','regex:/^\d*(\.\d{1,2})?$/', 'max:255'],
        ];
    }
}
