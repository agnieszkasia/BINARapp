<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Support\Facades\Redirect;


class TaxSettlementController extends Controller{

    public function show(){
        $invoice = null;
        $values = null;

        if ($_FILES['file']['tmp_name'][0] == "" ){
            return Redirect::back()->withErrors(['Nie wybrano żadnych plików']);
        }

        $filesPaths = $_FILES['file']['tmp_name'];
        foreach ($filesPaths as $key => $filePath) {

            $reader = ReaderEntityFactory::createODSReader();
            $reader->setShouldPreserveEmptyRows(true);

            $reader->open($filePath);

            foreach ($reader->getSheetIterator() as $sheet) {

                $j = 0;
                foreach ($sheet->getRowIterator() as $row) {

                    $cells = $row->getCells();
                    foreach ($cells as $cell) {
                        $values[$key][$j][] = $cell->getValue();
                    }
                    $j++;
                }
            }
            $reader->close();
        }


        foreach ($values as $key => $invoice){
            $invoices[$key]['issue_date'] = $values[$key][1][6];
            $invoices[$key]['due_date'] = $values[$key][2][6];

            $fileName = $_FILES['file']['name'][$key];
            $invoices[$key]['invoice_number'] = substr($fileName, 4, 3).$values[$key][5][6];

            $invoices[$key]['company'] = $values[$key][9][1];

            $invoices[$key]['address'] = $values[$key][11][1];
            if (isset($values[$key][12][1])) $invoices[$key]['address'] .= " ".$values[$key][12][1];

            if (!empty($values[$key][13][1])) {
                $invoices[$key]['NIP'] = $values[$key][13][1];
                $invoices[$key]['NIP'] = str_replace(["NIP", ":", " ", "-", ",", "\xc2\xa0"],"",$invoices[$key]['NIP']);
            }
            else $invoices[$key]['NIP'] = "brak";

            for ($i = 19; $i<=33; $i++) {
                if (!empty($values[$key][$i][1]) && ($values[$key][$i][4] == 'szt' || str_contains($values[$key][$i][4], 'm'))) {
                    if (empty($invoices[$key]['products_names'])) {
                        $invoices[$key]['products_names'] = $values[$key][$i][3]."x ".$values[$key][$i][1];
                        $invoices[$key]['products_number'] = 1;

                    } else {
                        $invoices[$key]['products_names'] .= ", ".$values[$key][$i][3]."x ". $values[$key][$i][1];
                        $invoices[$key]['products_number']++;
                    }

                    if (empty($invoices[$key]['products'])) {
                        $invoices[$key]['products'] = $values[$key][$i][9];
                    } else $invoices[$key]['products'] += $values[$key][$i][9];

                } elseif (empty($invoices[$key]['products_names'])) {
                    $invoices[$key]['products_names'] = "brak produktów";
                    $invoices[$key]['products_number'] = 0;
                    $invoices[$key]['products'] = 0;
                }

                if (!empty($values[$key][$i][1]) && str_contains($values[$key][$i][4], 'usł')) {
                    if (empty($invoices[$key]['service'])) {
                        $invoices[$key]['service'] = $values[$key][$i][9];
                    } else $invoices[$key]['service'] += $values[$key][$i][9];

                } elseif (empty($invoices[$key]['service'])) {
                    $invoices[$key]['service'] = "0";

                }
            }


            $invoices[$key]['netto'] = $values[$key][34][6];
            $invoices[$key]['vat'] = $values[$key][34][8];
            $invoices[$key]['brutto'] = $values[$key][34][9];
        }
        return view('show_invoices', compact('invoices'));
    }

    public function showAddSalesPage(Request $request){
        $invoices = json_decode($request['invoices'], true);

        return view('add_sales', compact('invoices'));
    }

    public function showAddPurchasesPage(Request $request){
        $invoices = json_decode($request['invoices'], true);
        foreach ($request['due_date'] as $key=> $product) {
            $sales[$key]['due_date'] = $request['due_date'][$key];
            $sales[$key]['products_names'] = $request['products_names'][$key];
            $sales[$key]['netto'] = round($request['products'][$key] - ($request['products'][$key]*0.23),2);
            $sales[$key]['vat'] = round($request['products'][$key] * 0.23,2);
            $sales[$key]['brutto'] = $request['products'][$key];
        }

        return view('add_purchases', compact('invoices', 'sales'));
    }

    public function showSummaryPage(Request $request){
        $sales = json_decode($request['sales'], true);
        $invoices = json_decode($request['invoices'], true);

        foreach ($request['issue_date'] as $key => $item){
            $purchases[$key]['issue_date'] = $request['issue_date'][$key];
            $purchases[$key]['due_date'] = $request['due_date'][$key];
            $purchases[$key]['invoice_number'] = $request['invoice_number'][$key];
            $purchases[$key]['company'] = $request['company'][$key];
            $purchases[$key]['address'] = $request['address'][$key];
            $purchases[$key]['NIP'] = $request['NIP'][$key];
            $purchases[$key]['netto'] = $request['netto'][$key];
            $purchases[$key]['vat'] = $request['vat'][$key];
            $purchases[$key]['brutto'] = $request['brutto'][$key];
        }

        $purchasesNetto = 0;
        $purchasesVat = 0;
        $purchasesBrutto = 0;

        foreach ($purchases as $purchase){
            $purchasesNetto += $purchase['netto'];
            $purchasesVat += $purchase['vat'];
            $purchasesBrutto += $purchase['brutto'];
        }

        $invoicesNetto = 0;
        $invoicesVat = 0;
        $invoicesBrutto = 0;

        foreach ($invoices as $invoice){
            $invoicesNetto += $invoice['netto'];
            $invoicesVat += $invoice['vat'];
            $invoicesBrutto += $invoice['brutto'];
        }

        $undefinedSalesNetto = 0;
        $undefinedSalesVat = 0;
        $undefinedSalesBrutto = 0;

        foreach ($sales as $sale) {
            $undefinedSalesNetto += $sale['netto'];
            $undefinedSalesVat += $sale['vat'];
            $undefinedSalesBrutto += $sale['brutto'];
        }

        $salesNetto = $invoicesNetto + $undefinedSalesNetto;
        $salesVat = $invoicesVat + $undefinedSalesVat;
        $salesBrutto = $invoicesBrutto + $undefinedSalesBrutto;

        $netto = $salesNetto - $purchasesNetto;
        $vat = $salesVat - $purchasesVat;
        $brutto = $salesBrutto - $purchasesBrutto;

        return view('summary', compact('netto','vat', 'brutto',
                            'purchasesNetto', 'purchasesVat', 'purchasesBrutto',
                            'invoicesNetto', 'invoicesVat', 'invoicesBrutto',
                            'undefinedSalesNetto', 'undefinedSalesVat', 'undefinedSalesBrutto',
                            'salesNetto', 'salesVat', 'salesBrutto',
                            'invoices', 'sales','purchases'));

    }

    public function generateCSVFile(Request $request){

        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=CSV.csv');
        header("Content-Transfer-Encoding: UTF-8");

        $invoices = json_decode($request['invoices'], true);

        $vat = 0;

        foreach ($invoices as $key =>$invoice){
            $invoice['issue_date'];

            $issueDateTime = DateTime::createFromFormat('d.m.Y', $invoice['issue_date']);
            $invoice['issue_date'] = $issueDateTime->format('Y-m-d');

            $dueDateTime = DateTime::createFromFormat('d.m.Y', $invoice['due_date']);
            $invoice['due_date'] = $dueDateTime->format('Y-m-d');

            $vat += $invoice['vat'];

            $invoice['netto'] = str_replace(".",",",$invoice['netto']);
            $invoice['vat'] = str_replace(".",",",$invoice['vat']);

            $invoice['company'] = str_replace("\"","",$invoice['company']);


            $lines[] = ";;;;;;;;;;;;".($key+1).";".
                $invoice['NIP'].";".
                $invoice['company'].";".
                $invoice['address'].";".
                $invoice['invoice_number'].";".
                $invoice['issue_date'].";".
                $invoice['due_date'].";;;;;;;;;;".
                $invoice['netto'].";".
                $invoice['vat'].";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;";
        }

        $purchases = json_decode($request['purchases'], true);

        foreach ($purchases as $key =>$purchase){
            $purchase['issue_date'];

            $issueDateTime = DateTime::createFromFormat('d.m.Y', $purchase['issue_date']);
            $purchase['issue_date'] = $issueDateTime->format('Y-m-d');

            $dueDateTime = DateTime::createFromFormat('d.m.Y', $purchase['due_date']);
            $purchase['due_date'] = $dueDateTime->format('Y-m-d');

            $purchase['netto'] = str_replace(".",",",$purchase['netto']);
            $purchase['vat'] = str_replace(".",",",$purchase['vat']);

            $purchaseLines[] = ";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;".($key+1).";".
                $purchase['NIP'].";".
                $purchase['company'].";".
                $purchase['address'].";".
                $purchase['invoice_number'].";".
                $purchase['issue_date'].";".
                $purchase['due_date'].";;;".
                $purchase['netto'].";".
                $purchase['vat'].";;;;;;";
        }

        $fp = fopen('php://output', 'a'); // Configure fopen to write to the output buffer

        $data = 'KodFormularza;kodSystemowy;wersjaSchemy;WariantFormularza;CelZlozenia;DataWytworzeniaJPK;DataOd;DataDo;NazwaSystemu;NIP;PelnaNazwa;Email;LpSprzedazy;NrKontrahenta;NazwaKontrahenta;AdresKontrahenta;DowodSprzedazy;DataWystawienia;DataSprzedazy;K_10;K_11;K_12;K_13;K_14;K_15;K_16;K_17;K_18;K_19;K_20;K_21;K_22;K_23;K_24;K_25;K_26;K_27;K_28;K_29;K_30;K_31;K_32;K_33;K_34;K_35;K_36;K_37;K_38;K_39;LiczbaWierszySprzedazy;PodatekNalezny;LpZakupu;NrDostawcy;NazwaDostawcy;AdresDostawcy;DowodZakupu;DataZakupu;DataWplywu;K_43;K_44;K_45;K_46;K_47;K_48;K_49;K_50;LiczbaWierszyZakupow;PodatekNaliczony'.PHP_EOL.
                'JPK_VAT;JPK_VAT (3);1-1;3;0;2020-09-31T09:30:47;2021-04-01;2021-04-30;OpenOffice Calc;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;'.PHP_EOL.
                ';;;;;;;;;7121553440;BINAR Jarosław Glinka;jarb23@wp.pl;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;'.PHP_EOL.
                ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;'.PHP_EOL;

        foreach ($lines as $line) {
            $data .= $line.PHP_EOL;
        }

        $undefinedSalesNetto = str_replace(".",",",$request['undefinedSalesNetto']);
        $undefinedSalesVat = str_replace(".",",",$request['undefinedSalesVat']);
        $salesVat = str_replace(".",",",$request['salesVat']);

        $data .= ";;;;;;;;;;;;".(count($invoices)+1).";brak;sprzedaz bezrachunkowa miesiąc;brak;brak;;2021-04-30;;;;;;;;;;".$undefinedSalesNetto.";".$undefinedSalesVat.";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;".PHP_EOL;

        $data .= ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;'.(count($invoices)+1).';'.$salesVat.';;;;;;;;;;;;;;;;;'.PHP_EOL;

        foreach ($purchaseLines as $line) {
            $data .= $line.PHP_EOL;
        }
        $purchaseVat = str_replace(".",",",$request['purchaseVat']);
        $data .= ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;'.count($purchases).';'.$purchaseVat;



        fwrite($fp, print_r($data, TRUE));

        fclose($fp);
    }


}
