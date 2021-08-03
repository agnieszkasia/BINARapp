<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getMonthName($stringDate): string{ //date format - d.m.Y

        $lastDayOfMonth = date_format(date_create_from_format('d.m.Y', $stringDate), 'Y-m-t');

        setlocale(LC_ALL, 'pl', 'pl', 'polish', 'Polish');
        $monthName = iconv('ISO-8859-2', 'UTF-8',(strftime('%B', strtotime($lastDayOfMonth))));
        return ucfirst($monthName);
    }
}
