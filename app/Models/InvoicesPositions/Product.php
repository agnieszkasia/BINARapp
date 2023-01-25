<?php

namespace App\Models\InvoicesPositions;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends InvoicePosition
{
    use HasFactory;

    public function __construct($type)
    {
        $this->setAttribute('type', $type);

        parent::__construct();
    }
}
