<?php

namespace App\Services;

use App\Models\Invoices\SaleInvoice;
use App\Models\InvoicesPostions\Product;
use App\Models\InvoicesPostions\Service;
use DateTime;

class InvoiceService
{
    public function create($values, $company)
    {
        $saleInvoice = new SaleInvoice();
        $saleInvoice->setAttribute('invoice_number', $values[5][6]);
        $saleInvoice->setAttribute('issue_date', DateTime::createFromFormat('d.m.Y', $values[1][6])->format('Y-m-d'));
        $saleInvoice->setAttribute('due_date', DateTime::createFromFormat('d.m.Y', $values[2][6])->format('Y-m-d'));
        $saleInvoice->setAttribute('gross', $values[34][9]);
        $saleInvoice->setAttribute('net', $values[34][6]);
        $saleInvoice->setAttribute('vat', $values[34][8]);
        $saleInvoice->setAttribute('company_id', $company->id);
        $saleInvoice->save();

        for ($i = 19; $i <= 33; $i++) {
            if (!empty($values[$i][1]) && ($values[$i][4] == 'szt' || str_contains($values[$i][4], 'm'))) {
                $invoicePosition = new Product();
            } elseif (!empty($values[$i][1]) && str_contains($values[$i][4], 'usł')) {
                $invoicePosition = new Service();
            } else {
                unset($invoicePosition);
            }

            if (isset($invoicePosition)) {
                $invoicePosition->setAttribute('name', $values[$i][1]);
                $invoicePosition->setAttribute('quantity', $values[$i][3]);
                $invoicePosition->setAttribute('gross', $values[$i][9]);
                $invoicePosition->setAttribute('net', $values[$i][6]);
                $invoicePosition->setAttribute('vat_rate', $values[$i][7]);
                $invoicePosition->setAttribute('vat', $values[$i][8]);
                $invoicePosition->setAttribute('invoice_id', $saleInvoice->id);
                $invoicePosition->save();
            }
        }

        return $saleInvoice;
    }

    public function validate($request)
    {
        $request->validate([
            'companyName' => ['required', 'string', 'max:255', 'min:2'],
            'firstname' => ['required','string', 'max:255', 'min:2'],
            'lastname' => ['required', 'string', 'max:255', 'min:2'],
            'address' => ['required', 'string', 'max:255', 'min:2'],
            'birthDate' => ['required', 'string','regex:/19[0-9]{2}|200[0,1,2,3]-[0-9]{2}-[0-9]{2}/u', 'max:255'],
            'mail' => ['required', 'string', 'email', 'max:255'],
            'phoneNumber' => ['required', 'string','regex:/^[0-9]{9}/u', 'size:9'],
            'NIP' => ['required', 'string', 'regex:/[0-9]{10}/u', 'size:10'],
            'taxOfficeCode' => ['required', 'string'],
            'file' => ['required'],
        ]);
    }
}
