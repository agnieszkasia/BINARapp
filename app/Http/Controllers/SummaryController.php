<?php

namespace App\Http\Controllers;

class SummaryController extends Controller
{
    public function index()
    {
        $sales = [];
        $invoices = [];
        $purchases = [];

        $purchasesNetto = 0;
        $purchasesVat = 0;
        $purchasesBrutto = 0;

        if (isset($purchases[0]['issue_date'])) {
            foreach ($purchases as $purchase) {
                $purchasesNetto += $purchase['netto'];
                $purchasesVat += $purchase['vat'];
                $purchasesBrutto += $purchase['brutto'];
            }
        }

        $invoicesNetto = 0;
        $invoicesVat = 0;
        $invoicesBrutto = 0;

        foreach ($invoices as $invoice) {
            $invoicesNetto += $invoice['netto'];
            $invoicesVat += $invoice['vat'];
            $invoicesBrutto += $invoice['brutto'];
        }

        $undefinedSalesNetto = 0;
        $undefinedSalesVat = 0;
        $undefinedSalesBrutto = 0;

        if (isset($sales[0]['due_date'])) {
            foreach ($sales as $sale) {
                $undefinedSalesNetto += $sale['netto'];
                $undefinedSalesVat += $sale['vat'];
                $undefinedSalesBrutto += $sale['brutto'];
            }
        }

        $salesNetto = $invoicesNetto + $undefinedSalesNetto;
        $salesVat = $invoicesVat + $undefinedSalesVat;
        $salesBrutto = $invoicesBrutto + $undefinedSalesBrutto;

        $netto = $salesNetto - $purchasesNetto;
        $vat = $salesVat - $purchasesVat;
        $brutto = $salesBrutto - $purchasesBrutto;

        return view('summary', compact('netto', 'vat', 'brutto',
            'purchasesNetto', 'purchasesVat', 'purchasesBrutto',
            'invoicesNetto', 'invoicesVat', 'invoicesBrutto',
            'undefinedSalesNetto', 'undefinedSalesVat', 'undefinedSalesBrutto',
            'salesNetto', 'salesVat', 'salesBrutto'));
    }}
