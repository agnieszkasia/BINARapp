<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use DateTime;
use DOMDocument;
use finfo;
use Illuminate\Http\Request;

class CSVFileController extends Controller{
    public function generateCSVFile($request, $company){

        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=CSV.csv');
        header("Content-Transfer-Encoding: UTF-8");

        $lines = array();
        $purchaseLines = array();

        $invoices = session('invoices');

        $vat = 0;

        foreach ($invoices as $key => $invoice) {
            $invoice['issue_date'];

            $issueDateTime = DateTime::createFromFormat('d.m.Y', $invoice['issue_date']);
            $invoice['issue_date'] = $issueDateTime->format('Y-m-d');

            $dueDateTime = DateTime::createFromFormat('d.m.Y', $invoice['due_date']);
            $invoice['due_date'] = $dueDateTime->format('Y-m-d');

            $vat += $invoice['vat'];

            $invoice['netto'] = str_replace(".", ",", $invoice['netto']);
            $invoice['vat'] = str_replace(".", ",", $invoice['vat']);

            $invoice['company'] = str_replace("\n", " ", str_replace("\"", "", $invoice['company']));


            $lines[] = ";;;;;;;;;;;;" . ($key + 1) . ";" .
                $invoice['NIP'] . ";" .
                $invoice['company'] . ";" .
                $invoice['address'] . ";" .
                $invoice['invoice_number'] . ";" .
                $invoice['issue_date'] . ";" .
                $invoice['due_date'] . ";;;;;;;;;;" .
                $invoice['netto'] . ";" .
                $invoice['vat'] . ";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;";
        }

        $purchases = session('purchases');

        $sort = null;
        foreach ($purchases as $key => $purchase) {
            $sort[$key] = strtotime($purchase['issue_date']);
        }

        array_multisort($sort, SORT_ASC, $purchases);

        foreach ($purchases as $key => $purchase) {

            $issueDateTime = DateTime::createFromFormat('d.m.Y', $purchase['issue_date']);
            $purchase['issue_date'] = $issueDateTime->format('Y-m-d');

            $dueDateTime = DateTime::createFromFormat('d.m.Y', $purchase['due_date']);
            $purchase['due_date'] = $dueDateTime->format('Y-m-d');

            $purchase['netto'] = str_replace(".", ",", $purchase['netto']);
            $purchase['vat'] = str_replace(".", ",", $purchase['vat']);

            $purchaseLines[] = ";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;" . ($key + 1) . ";" .
                $purchase['NIP'] . ";" .
                $purchase['company'] . ";" .
                $purchase['address'] . ";" .
                $purchase['invoice_number'] . ";" .
                $purchase['issue_date'] . ";" .
                $purchase['due_date'] . ";;;" .
                $purchase['netto'] . ";" .
                $purchase['vat'] . ";;;;;;";
        }

        $undefinedSalesNetto = str_replace(".", ",", $request['undefinedSalesNetto']);
        $undefinedSalesVat = str_replace(".", ",", $request['undefinedSalesVat']);
        $salesVat = str_replace(".", ",", $request['salesVat']);


        $stringDate = $invoices[count($invoices)-1]['due_date'];
        $undocumentedSaleDate = $lastDayOfMonth = date_format(date_create_from_format('d.m.Y', $stringDate), 'Y-m-t');
        $firstDayOfMonth = date_format(date_create_from_format('d.m.Y', $stringDate), 'Y-m-t');

        setlocale(LC_ALL, 'pl', 'pl_PL', 'pl_PL.ISO8859-2', 'plk', 'polish', 'Polish');
        $monthName = strftime('%B', strtotime($undocumentedSaleDate));

        $fp = fopen('php://output', 'a');

        $data = 'KodFormularza;kodSystemowy;wersjaSchemy;WariantFormularza;CelZlozenia;DataWytworzeniaJPK;DataOd;DataDo;NazwaSystemu;NIP;PelnaNazwa;Email;LpSprzedazy;NrKontrahenta;NazwaKontrahenta;AdresKontrahenta;DowodSprzedazy;DataWystawienia;DataSprzedazy;K_10;K_11;K_12;K_13;K_14;K_15;K_16;K_17;K_18;K_19;K_20;K_21;K_22;K_23;K_24;K_25;K_26;K_27;K_28;K_29;K_30;K_31;K_32;K_33;K_34;K_35;K_36;K_37;K_38;K_39;LiczbaWierszySprzedazy;PodatekNalezny;LpZakupu;NrDostawcy;NazwaDostawcy;AdresDostawcy;DowodZakupu;DataZakupu;DataWplywu;K_43;K_44;K_45;K_46;K_47;K_48;K_49;K_50;LiczbaWierszyZakupow;PodatekNaliczony' . PHP_EOL .
            'JPK_VAT;JPK_VAT (3);1-1;3;0;'.$lastDayOfMonth.'T23:59:59;'.$firstDayOfMonth.';'.$lastDayOfMonth.';OpenOffice Calc;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;' . PHP_EOL .
            ';;;;;;;;;'.$company["NIP"].';'.$company["companyName"].';'.$company["mail"].';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;' . PHP_EOL .
            ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;' . PHP_EOL;

        foreach ($lines as $line) {
            $data .= $line . PHP_EOL;
        }

        $data .= ";;;;;;;;;;;;" . (count($invoices) + 1) . ";brak;sprzedaz bezrachunkowa miesiąc ".$monthName.";brak;brak;;".$undocumentedSaleDate.";;;;;;;;;;" . $undefinedSalesNetto . ";" . $undefinedSalesVat . ";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;" . PHP_EOL;

        $data .= ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;' . (count($invoices) + 1) . ';' . $salesVat . ';;;;;;;;;;;;;;;;;' . PHP_EOL;

        foreach ($purchaseLines as $line) {
            $data .= $line . PHP_EOL;
        }
        $purchaseVat = str_replace(".", ",", $request['purchasesVat']);
        $data .= ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;' . count($purchases) . ';' . $purchaseVat;


        fwrite($fp, print_r($data, TRUE));

        fclose($fp);
    }
}
