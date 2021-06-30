<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;

class ODSFileController extends Controller
{
    public function generateDZSVFile($request){ //DZSV - Dzienne Zestawienie Sprzedaży Vat
        $invoices = session('invoices');
        $sales = session('sales');

        if (isset($sales)) {
            $sales = $this->sortUndocumentedSales($sales);

            $allSales = array_merge($invoices, $sales);
        } else $allSales = $invoices;


        foreach ($allSales as $key => $sale) {
            $sort[$key] = strtotime($sale['due_date']);
        }

        array_multisort($sort, SORT_ASC, $allSales);

        $spreadsheet = new Spreadsheet();

        $i = 0;

        foreach ($allSales as $key => $sale) {

            if ($sale['brutto'] !== null && !isset($sale['products']) && !isset($sale['service'])) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . ($key + 1 + $i), $key + 1 + $i);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . ($key + 1 + $i), $sale['due_date']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . ($key + 1 + $i), "Sprzedaż nieudokumentowana - " . $sale['products_names']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . ($key + 1 + $i), $sale['brutto']);
            } elseif (isset($sale['products']) && $sale['products'] !== 0) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . ($key + 1 + $i), $key + 1 + $i);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . ($key + 1 + $i), $sale['due_date']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . ($key + 1 + $i), $sale['company'] . " " . $sale['address'] . " " . $sale['NIP']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('D' . ($key + 1 + $i), $sale['invoice_number']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . ($key + 1 + $i), $sale['products']);
            } elseif (isset($sale['products']) && $sale['products'] == 0) $i--;

            if (isset($sale['service']) && $sale['service'] !== "0") {
                $i++;
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . ($key + 1 + $i), $key + 1 + $i);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . ($key + 1 + $i), $sale['due_date']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . ($key + 1 + $i), $sale['company'] . " " . $sale['address'] . " " . $sale['NIP']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('D' . ($key + 1 + $i), $sale['invoice_number']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . ($key + 1 + $i), $sale['service']);
            }
        }

        $writer = new Ods($spreadsheet);
        $writer->save('DZSV.ods');

        $finfo = new finfo(FILEINFO_MIME);
        header('Content-Type: ' . $finfo->file(public_path('DZSV.ods')));
        header('Content-Disposition: attachment; filename="DZSV.ods"');

        readfile(public_path("DZSV.ods"));

        unlink(public_path('DZSV.ods'));
    }

    public function sortUndocumentedSales($sales){
        foreach ($sales as $key => $sale) {
            $sort[$key] = strtotime($sale['due_date']);
        }

        array_multisort($sort, SORT_ASC, $sales);

        foreach ($sales as $key => $sale) {
            if (isset($sales[$key - 1])) $previousSale = $sales[$key - 1];

            if (isset($previousSale) && $previousSale['due_date'] == $sale['due_date']) {
                unset($sales[$key - 1]);

                $sales[$key]['products_names'] = $sale['products_names'] . ", " . $previousSale['products_names'];
                $sales[$key]['netto'] = $sale['netto'] + $previousSale['netto'];
                $sales[$key]['vat'] = $sale['vat'] + $previousSale['vat'];
                $sales[$key]['brutto'] = $sale['brutto'] + $previousSale['brutto'];
            }

        }

        return $sales;
    }

    public function generateRZVFile($request){ //RZV - Rejestr Zakupów Vat
        $purchases = session('purchases');

        if (isset($purchases['due_date'])) {
            foreach ($purchases as $key => $purchase) {
                $sort[$key] = strtotime($purchase['due_date']);
            }

            array_multisort($sort, SORT_ASC, $purchases);
        }
        $spreadsheet = new Spreadsheet();

        foreach ($purchases as $key => $purchase) {
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . ($key + 1), $key + 1);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . ($key + 1), $purchase['invoice_number']);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . ($key + 1), $purchase['due_date']);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('D' . ($key + 1), $purchase['issue_date']);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . ($key + 1), $purchase['company']);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('F' . ($key + 1), $purchase['address']);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('G' . ($key + 1), $purchase['NIP']);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('H' . ($key + 1), $purchase['brutto']);
        }

        $writer = new Ods($spreadsheet);
        $writer->save('RZV.ods');

        $finfo = new finfo(FILEINFO_MIME);
        header('Content-Type: ' . $finfo->file(public_path('RZV.ods')));
        header('Content-Disposition: attachment; filename="RZV.ods"');

        readfile(public_path("RZV.ods"));

        unlink(public_path('RZV.ods'));
    }
}
