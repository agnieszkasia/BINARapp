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

//                $values = array();

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
                $invoices[$i]['company'] = $values[$i][8][0];



                $row = 0;
                if (isset($values[$i][12+$row][0]) && str_contains($values[$i][12+$row][0], 'Forma')) {
                        $invoices[$i]['address'] = $values[$i][10+$row][0];
                    if (isset($values[$i][11+$row][0])){
                        $invoices[$i]['NIP'] = $values[$i][11+$row][0];
                    } else $invoices[$i]['NIP'] = 'brak';
                } else{
                        $invoices[$i]['address'] = $values[$i][10+$row][0] . " " . $values[$i][11+$row][0];
                    if (isset($values[$i][12+$row][0])) {

                        $invoices[$i]['NIP'] = $values[$i][12 + $row][0];
                    } else $invoices[$i]['NIP'] = 'brak';
                }
                $invoices[$i]['NIP'] = str_replace(["NIP", ":", " ", "-", ","],"",$invoices[$i]['NIP']);
//
//
                if (isset($values[$i][15+$row][0]) && $values[$i][15+$row][0] == 'LP') $row = $row +1;
                elseif (isset($values[$i][16+$row][0]) && $values[$i][16+$row][0] == 'LP') $row = $row + 2;
                elseif (isset($values[$i][17+$row][0]) && $values[$i][17+$row][0] == 'LP') $row = $row +3;
//
                $invoices[$i]['product'] = $values[$i][16+$row][8];
//
                if(isset($values[$i][17+$row][8])) {
                    $invoices[$i]['product2'] = $values[$i][17 + $row][8];
                }

                if(isset($values[$i][18+$row][8])) {
                    $invoices[$i]['product3'] = $values[$i][18 + $row][8];
                } else $row = $row+1;

                if(isset($values[$i][19+$row][8])) {
                    $invoices[$i]['product4'] = $values[$i][19 + $row][8];
                } else $row = $row+1;

                if (isset($values[$i][28+$row][0]) && str_contains($values[$i][28+$row][0], 'Razem')) $invoices[$i]['netto'] = $values[$i][28+$row][1];
                else $invoices[$i]['netto'] = 'brak';

                    if (isset($values[$i][28+$row][2]))$invoices[$i]['vat'] = $values[$i][28+$row][2];
                else $invoices[$i]['vat'] = 'brak';
                    if (isset($values[$i][28+$row][3]))$invoices[$i]['brutto'] = $values[$i][28+$row][3];
                else $invoices[$i]['brutto'] = 'brak';

            }

            $reader->close();
            $i++;
        }

//        dd($values[25], $values[26],$values[90]);
//        dd($values[30], $values[31]);
//        dd($values);

//        generateDailySalesStatementFile();
//        generateCSVFile();
//        generateXMLfile();

        return view('show_invoices', compact('invoices'));
    }

    public function array_search_partial($arr, $keyword) {
    foreach($arr as $index => $string) {
        if (strpos($string, $keyword) !== FALSE)
            return $index;
        }
    }

}
