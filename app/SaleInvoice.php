<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleInvoice extends Invoice
{
    use HasFactory;

    private int $vatRate = 8;

    public function __construct()
    {
        parent::__construct();
    }
}
