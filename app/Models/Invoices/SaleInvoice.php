<?php

namespace App\Models\Invoices;

use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleInvoice extends Invoice
{
    use HasFactory;

    private int $vatRate = 8;

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function __construct()
    {
        parent::__construct();
    }
}
