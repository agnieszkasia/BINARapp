<?php

namespace App\Models\Invoices;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleInvoice extends Invoice
{
    use HasFactory;

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
