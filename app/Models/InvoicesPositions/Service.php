<?php

namespace App\Models\InvoicesPositions;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends InvoicePosition
{
    use HasFactory;

    public function __construct($type)
    {
        $this->setAttribute('type', $type);

        parent::__construct();
    }
}
