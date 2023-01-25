<?php

namespace App\Services;

use App\Models\Invoices\CorrectiveInvoice;
use DateTime;
use Illuminate\Http\Request;

class CorrectiveInvoiceService
{
    public function createNewCorrectiveInvoice(Request $request): CorrectiveInvoice
    {
        $correctiveInvoice = new CorrectiveInvoice();
        $correctiveInvoice->setAttribute('invoice_number', $request['invoice_number']);
        $correctiveInvoice->setAttribute('issue_date', DateTime::createFromFormat('d.m.Y', $request['issue_date'])->format('Y-m-d'));
        $correctiveInvoice->setAttribute('due_date', DateTime::createFromFormat('d.m.Y', $request['due_date'])->format('Y-m-d'));
        $correctiveInvoice->setAttribute('gross', str_replace(",", ".", $request['gross']));
        $correctiveInvoice->setAttribute('net', str_replace(",", ".", $request['net']));
        $correctiveInvoice->setAttribute('vat', str_replace(",", ".", $request['vat']));
        $correctiveInvoice->setAttribute('company_id', 1); //tutaj jeszcze trzeba poprawne id wpisywać
        $correctiveInvoice->save();

        return $correctiveInvoice;
    }
}
