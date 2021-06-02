<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;


class TaxSettlementController extends Controller{

    public function show()
    {
        $invoice = null;

        $filespaths = $_FILES['file']['tmp_name'];
        $i = 0;
        foreach ($filespaths as $filepath) {

            $reader = ReaderEntityFactory::createODSReader();

            $reader->open($filepath);

            foreach ($reader->getSheetIterator() as $sheet) {

                $j = 0;
                foreach ($sheet->getRowIterator() as $row) {

                    $cells = $row->getCells();
                    foreach ($cells as $cell) {
                        $cell = $cell->getValue();
                        if ($cell !== "" and $cell !== " ") {
                            $values[$i][$j][] = $cell;
                        }
                    }
                    $j++;

                }

                $invoices[$i]['issue_date'] = $values[$i][1][2];
                $invoices[$i]['due_date'] = $values[$i][2][2];
                $invoices[$i]['invoice_number'] = $values[$i][5][1] . $values[$i][5][2];




            }

            $reader->close();
            $i++;
        }

        $i=0;
        foreach ($values as $invoice){
            $j=0;
            $previousKey = 0;
            foreach ($invoice as $key => $row){
                foreach ($row as  $cell){

                    if (str_contains($cell, 'Nabywca') ) {
                        $invoices[$i]['company'] = $invoice[$j+2][0];
                    }

                    if (str_contains($cell, 'Adres') ) {
                        if (isset($invoice[$j+3][0])) {
                            $numeric = str_replace(["NIP", ":", " ", "-", ",", "\xc2\xa0"],"",$invoice[$j+3][0]);
                            if (!is_numeric($numeric)){
                                $invoices[$i]['address'] = $invoice[$j+2][0]." ".$invoice[$j+3][0];
                            } else $invoices[$i]['address'] = $invoice[$j+2][0];
                        }
                        else $invoices[$i]['address'] = $invoice[$j+2][0];
                    }

                    if (str_contains($cell, 'Forma') and $j>10) {

                        $invoices[$i]['NIP'] = $invoice[$j][0];
                        $invoices[$i]['NIP'] = str_replace(["NIP", ":", " ", "-", ",", "\xc2\xa0"],"",$invoices[$i]['NIP']);
//
                        if (!is_numeric($invoices[$i]['NIP'])) $invoices[$i]['NIP'] = 'brak';
                    }
                    elseif(str_contains($cell, 'Forma płatności') and $j<=10) {
                        $invoices[$i]['NIP'] = 'brak';
                    }

                    if (str_contains($cell, 'Nazwa towaru') ) {
                        $firstProduct[$i] = $key+2;
                    }


                    if (str_contains($cell, 'Wysyłka') ) {
                        $invoices[$i]['service'] = $invoice[$key][8];
                        $lastProduct[$i] = $key-1;
                    } elseif (!isset($invoices[$i]['service'])){
                        $invoices[$i]['service'] = 0;
                    }


                    if (str_contains($cell, 'Razem') ) {
                        if (!isset($lastProduct[$i])) $lastProduct[$i] = $previousKey;
                        $invoices[$i]['netto'] = $invoice[$key][1];
                        $invoices[$i]['vat'] = $invoice[$key][2];
                        $invoices[$i]['brutto'] = $invoice[$key][3];
                    }

                    if (isset($firstProduct[$i]) && isset($lastProduct[$i]) && !isset($invoices[$i]['products_number'])){
                        $invoices[$i]['products_number'] = $lastProduct[$i] - $firstProduct[$i] + 1;

                        $invoices[$i]['products_names'] = $invoice[$firstProduct[$i]][1];
                        $invoices[$i]['products'] = $invoice[$firstProduct[$i]][8];
                        for ($k = $firstProduct[$i]+1; $k<= $lastProduct[$i]; $k++){
                            if (str_contains($invoice[$k][3], 'usł')){
                                $invoices[$i]['service'] = $invoices[$i]['service']+$invoice[$k][8];

                            } else{
                                $invoices[$i]['products_names'] = $invoices[$i]['products_names'].", ".$invoice[$k][1];
                                $invoices[$i]['products'] = $invoices[$i]['products'] + $invoice[$k][8];
                            }
                        }
                    }

                    $previousKey = $key;


                }
                $j++;
            }
//            dd($invoice);
            $i++;
//            $nip[] = $invoice[$key[$i]];
        }


//        dd($values);
//        dd($invoices);


//        generateDailySalesStatementFile();
//        generateCSVFile();
//        generateXMLfile();

        return view('show_invoices', compact('invoices'));
    }

}
