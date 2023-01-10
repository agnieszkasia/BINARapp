<?php

namespace App\Services\Readers;

class SaleStatementFileReader
{
    public function getDataFromFile($filesPaths)
    {
        foreach ($filesPaths as $filePath) {
            $file[] = $this->readCSV($filePath, array('delimiter' => ','));
            $undocumentedOrders =$this->getUndocumentedOrdersData($file);
            $items = $this->getItems($file);

            $i=0;
            foreach($items as $itemKey=>$item){
                foreach ($undocumentedOrders as $orderKey=>$order){
                    if(array_search($item[1],$order) !== false) {
                        $value[$i] = $itemKey . "  -  " . $orderKey;

                        $sales[$i]['issue_date'] = date("d.m.Y",strtotime($order[4]));
                        $sales[$i]['due_date'] = date("d.m.Y",strtotime($order[4]));
                        $sales[$i]['products_names'] = $item[5];


                        $products = $item[6] * $item[7];
                        $sales[$i]['netto'] = (float)round($products - ($products / 1.23), 2);
                        $sales[$i]['vat'] = (float)round($products / 1.23, 2);
                        $sales[$i]['brutto'] = (float)$products;
                        $sales[$i]['quantity'] = $item[6];
                        $sales[$i]['products'] = $item[7];

                        $i++;
                    }
                }
            }
        }

        return $sales;
    }

    public function readCSV($csvFile, $array)
    {
        $file_handle = fopen($csvFile, 'r');

        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 0, $array['delimiter']);
        }

        if (end($line_of_text)==false) array_pop($line_of_text);

        fclose($file_handle);
        return $line_of_text;
    }

    public function getUndocumentedOrdersData($file): array
    {
        $order = array();

        foreach ($file as $orders) {
            foreach ($orders as $key=>$sale) {
                if (!is_bool($sale) && $sale[0] == 'order' && $sale[5] == 'SENT' && $sale[37] == '') {
                    $order[] = $sale;
                }
            }
        }

        return $order;
    }

    public function getItems($file): array
    {
        $items = array();
        foreach ($file as $sales) {
            foreach ($sales as $key=>$sale) {
                if (!is_bool($sale) &&  $sale[0] == 'lineItem') {
                    $items[] = $sale;
                }
            }
        }
        return $items;
    }

}
