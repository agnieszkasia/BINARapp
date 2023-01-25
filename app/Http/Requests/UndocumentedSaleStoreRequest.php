<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UndocumentedSaleStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'undocumented_sales.*.due_date' => ['required', 'string', 'regex:/^(([0-2]{0,1}[0-9]{1})|(3[01]))\.[0-9]{2}\.(20[0-9]{2})$/u'],
            'undocumented_sales.*.products_names' => ['required', 'string', 'max:255', 'min:1'],
            'undocumented_sales.*.quantity' => ['required', 'integer'],
            'undocumented_sales.*.gross' => ['required', 'regex:/^\d{0,8}((\.|\,)\d{1,4})?$/u', 'max:255'],
        ];
    }
}
