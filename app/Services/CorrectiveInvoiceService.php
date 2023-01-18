<?php

namespace App\Services;

use App\Models\Invoices\CorrectiveInvoice;
use DateTime;
use Illuminate\Http\Request;

class CorrectiveInvoiceService
{
    public function create(Request $request): CorrectiveInvoice
    {
        $correctiveInvoice = new CorrectiveInvoice();
        $correctiveInvoice->setAttribute('invoice_number', $request['invoice_number']);
        $correctiveInvoice->setAttribute('issue_date', DateTime::createFromFormat('Y-m-d', $request['issue_date'])->format('Y-m-d'));
        $correctiveInvoice->setAttribute('due_date', DateTime::createFromFormat('Y-m-d', $request['due_date'])->format('Y-m-d'));
        $correctiveInvoice->setAttribute('gross', str_replace(",", ".", $request['gross']));
        $correctiveInvoice->setAttribute('net', str_replace(",", ".", $request['net']));
        $correctiveInvoice->setAttribute('vat', str_replace(",", ".", $request['vat']));
        $correctiveInvoice->setAttribute('company_id', 1);
        $correctiveInvoice->save();

        return $correctiveInvoice;
    }

    public function validate($request)
    {
        $request->validate([
            'issue_date' => ['required', 'date', 'date_multi_format:"Y-m-d"'],
            'due_date' => ['required', 'date', 'date_multi_format:"Y-m-d"'],
            'invoice_number' => ['required', 'string', 'max:255', 'min:2'],
            'nip' => ['required', 'string', 'regex:/[0-9]{10}/u', 'size:10'],
            'company' => ['required', 'string', 'max:255', 'min:2'],
            'address' => ['required', 'string', 'max:255', 'min:2'],
            'gross' => ['required','regex:/^\d*(\.\d{1,2})?$/', 'max:255'],
            'net' => ['required','regex:/^\d*(\.\d{1,2})?$/', 'max:255'],
            'vat' => ['required','regex:/^\d*(\.\d{1,2})?$/', 'max:255'],
        ]);
    }
}
