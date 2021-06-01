<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use function Psy\sh;


class TaxSettlementController extends Controller{

    public function show(){
        $invoice = null;

        $files = $_FILES['file']['tmp_name'];
//        dd($files);
        $i=0;
        foreach ($files as $file) {

            $filepath = $file;
            $reader = ReaderEntityFactory::createODSReader();

            $reader->open($filepath);


            foreach ($reader->getSheetIterator() as $sheet) {
                $j = 0;
                $values = null;


                foreach ($sheet->getRowIterator() as $row) {

                    $cells = $row->getCells();
                    foreach ($cells as $cell) {
                        $cell = $cell->getValue();
                        if ($cell !== "" && $cell !== " ") {
                            $values[$j][] = $cell;
                        }
                    }
                    $j++;

                }


                $invoices[$i]['issue_date'] = $values[1][2];
                $invoices[$i]['due_date'] = $values[2][2];
                $invoices[$i]['invoice_number'] = $values[5][1] . $values[5][2];
                $invoices[$i]['company'] = $values[8][0];

                $row= 0;
                if (isset($values[12+$row][0])) {
                    if (str_contains($values[12+$row][0], 'Forma')) {

                        $invoices[$i]['address'] = $values[10+$row][0];
                        if (isset($values[11][0])){
                            $invoices[$i]['NIP'] = $values[11+$row][0];
                        }
                        else $invoices[$i]['NIP'] = 'brak';
                    } else {
                        $invoices[$i]['address'] = $values[10+$row][0] . " " . $values[11+$row][0];
                        $invoices[$i]['NIP'] = $values[12+$row][0];
                    }
                    $invoices[$i]['NIP'] = str_replace(["NIP", ":", " ", "-", ","],"",$invoices[$i]['NIP']);
                } else{
                    $invoices[$i]['address'] = 'brak';
                    $invoices[$i]['NIP'] = 'brak';
                }


                if ($values[14+$row][0] == 'LP') $row = $row -1;
                elseif ($values[16+$row][0] == 'LP') $row = $row + 1;
                elseif ($values[17+$row][0] == 'LP') $row = $row +2;

                $invoices[$i]['product'] = $values[17+$row][8];

                if(isset($values[18+$row][8])) {
                    $invoices[$i]['product2'] = $values[18 + $row][8];
                } else $row = $row-1;

                if (isset($values[19+$row][8])) {
                    $invoices[$i]['product3'] = $values[19+$row][8];
                    $row = $row+1;
                }

                if (isset($values[20+$row][8])) {
                    $invoices[$i]['product3'] = $values[20+$row][8];
                    $row = $row+1;
                }

                if (isset($values[21+$row][8])) {
                    $invoices[$i]['product3'] = $values[21+$row][8];
                    $row = $row+1;
                }

                if (isset($values[29+$row][0])) $invoices[$i]['netto'] = $values[29+$row][0];
                else $invoices[$i]['netto'] = 'brak';
                if (isset($values[29+$row][2]))$invoices[$i]['vat'] = $values[29+$row][2];
            else $invoices[$i]['vat'] = 'brak';
                if (isset($values[29+$row][3]))$invoices[$i]['brutto'] = $values[29+$row][3];
            else $invoices[$i]['brutto'] = 'brak';

            }

            $reader->close();
            $i++;
        }

//        dd($values);

//        generateDailySalesStatementFile();
//        generateCSVFile();
//        generateXMLfile();

        return view('show_invoices', compact('invoices'));
    }


}
