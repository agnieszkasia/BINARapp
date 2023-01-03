<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends InvoicePosition
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'gross',
        'net',
        'vat',
    ];
}
