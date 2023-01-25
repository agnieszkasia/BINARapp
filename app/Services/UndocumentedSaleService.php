<?php

namespace App\Services;

use App\Models\Invoices\SaleInvoice;
use App\Models\InvoicesPostions\Product;
use App\Models\InvoicesPostions\Service;
use App\Models\UndocumentedSale;
use DateTime;

class UndocumentedSaleService
{
    public function createNewUndocumentedSale($values)
    {
        $saleInvoice = new UndocumentedSale();
        $saleInvoice->setAttribute('due_date', DateTime::createFromFormat('d.m.Y', $values['due_date'])->format('Y-m-d'));
        $saleInvoice->setAttribute('gross', $values['gross']);
        $saleInvoice->setAttribute('net', number_format($values['gross']/1.23,2,'.'));
        $saleInvoice->setAttribute('vat', number_format($values['gross']/1.23*0.23, 2, '.'));
        $saleInvoice->save();

        return $saleInvoice;
    }
}
