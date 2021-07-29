<?php

namespace App\Http\Controllers;

use finfo;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;

class ODSFileController extends Controller{

    public function generateSalesFile($detailed, $type, $filename){
        $invoices = session('invoices');
        $sales = session('sales');

        if (isset($sales)) {
            $sales = $this->sortItems($sales, 'false');
            $sales = $this->mergeSales($sales);

            $allSales = array_merge($invoices, $sales);
        } else $allSales = $invoices;

        $allSales = $this->sortItems($allSales, 'true');

        if ($type == 'DZSV') $spreadsheet = $this->createDZSVSpreadsheet($allSales, $detailed); //DZSV - Dzienne Zestawienie Sprzedaży Vat
        elseif ($type == 'KPiR') $spreadsheet = $this->createKSIESpreadsheet($allSales); //KPiR - Księga przychodów i rozchodów - podatek ryczałtowy

        $writer = new Ods($spreadsheet);
        $writer->save($filename);

        $finfo = new finfo(FILEINFO_MIME);
        header('Content-Type: ' . $finfo->file(public_path($filename)));
        header('Content-Disposition: attachment; filename='.$filename);

        readfile(public_path($filename));

        unlink(public_path($filename));
    }

    public function createDZSVSpreadsheet($allSales, $detailed): Spreadsheet{ //DZSV - Dzienne Zestawienie Sprzedaży Vat
        $i = 0;

        $spreadsheet = new Spreadsheet();

        foreach ($allSales as $key => $sale) {

            if ($sale['brutto'] !== null && !isset($sale['service'])) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . ($key + 1 + $i), $key + 1 + $i);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . ($key + 1 + $i), $sale['issue_date']);

                if ($detailed == 'true'){
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . ($key + 1 + $i), "Sprzedaż nieudokumentowana - " . $sale['products_names']);
                }elseif ($detailed == 'false') {
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . ($key + 1 + $i), "Sprzedaż nieudokumentowana");
                }

                $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . ($key + 1 + $i), $sale['brutto']);
            } elseif (isset($sale['products']) && $sale['products'] !== 0) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . ($key + 1 + $i), $key + 1 + $i);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . ($key + 1 + $i), $sale['issue_date']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . ($key + 1 + $i), $sale['company'] . " " . $sale['address'] . " " . $sale['NIP']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('D' . ($key + 1 + $i), $sale['invoice_number']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . ($key + 1 + $i), $sale['products']);
            } elseif (isset($sale['products']) && $sale['products'] == 0) $i--;

            if (isset($sale['service']) && $sale['service'] !== "0") {
                $i++;
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . ($key + 1 + $i), $key + 1 + $i);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . ($key + 1 + $i), $sale['issue_date']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . ($key + 1 + $i), $sale['company'] . " " . $sale['address'] . " " . $sale['NIP']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('D' . ($key + 1 + $i), $sale['invoice_number']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . ($key + 1 + $i), $sale['service']);
            }
        }
        return $spreadsheet;
    }

    public function createKSIESpreadsheet($allSales): Spreadsheet{ //KPiR - Księga przychodów i rozchodów - podatek ryczałtowy
        $i = 0;

        $spreadsheet = new Spreadsheet();

        foreach ($allSales as $key => $sale) {

            if ($sale['brutto'] !== null && !isset($sale['service'])) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . ($key + 1 + $i), $key + 1 + $i);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . ($key + 1 + $i), $sale['issue_date']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . ($key + 1 + $i), $sale['issue_date']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . ($key + 1 + $i), $sale['brutto']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('K' . ($key + 1 + $i), $sale['netto']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('L' . ($key + 1 + $i), $sale['netto']);
            } elseif (isset($sale['products']) && $sale['products'] !== 0) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . ($key + 1 + $i), $key + 1 + $i);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . ($key + 1 + $i), $sale['issue_date']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . ($key + 1 + $i), $sale['issue_date']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('D' . ($key + 1 + $i), $sale['invoice_number']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . ($key + 1 + $i), $sale['products']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('K' . ($key + 1 + $i), round($sale['products']/1.23,2));
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('L' . ($key + 1 + $i), round($sale['products']/1.23,2));
            } elseif (isset($sale['products']) && $sale['products'] == 0) $i--;

            if (isset($sale['service']) && $sale['service'] !== "0") {
                $i++;
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . ($key + 1 + $i), $key + 1 + $i);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . ($key + 1 + $i), $sale['issue_date']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . ($key + 1 + $i), $sale['issue_date']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('D' . ($key + 1 + $i), $sale['invoice_number']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . ($key + 1 + $i), $sale['service']);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('I' . ($key + 1 + $i), round($sale['service']/1.23, 2));
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('L' . ($key + 1 + $i), round($sale['service']/1.23, 2));
            }
        }
        return $spreadsheet;
    }

    public function mergeSales($sales){
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

    public function sortItems($sales, $multiSort){

        foreach ($sales as $key => $sale) {
            $sort[$key] = strtotime($sale['issue_date']);
            if ($multiSort == 'true') {
                if (isset($sale['invoice_number'])) $sort2[$key] = $sale['invoice_number'];
                else $sort2[$key] = '0';
            }
        }
        if ($multiSort =='true') {
            array_multisort($sort, SORT_ASC, $sort2, SORT_ASC, $sales);
        }
        else array_multisort($sort, SORT_ASC, $sales);

        return $sales;
    }

    public function generateRZVFile(){ //RZV - Rejestr Zakupów Vat
        $purchases = session('purchases');

        foreach ($purchases as $key => $purchase) {
            $sort[$key] = strtotime($purchase['issue_date']);
        }
        if (isset($sort)) array_multisort($sort, SORT_ASC, $purchases);

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
