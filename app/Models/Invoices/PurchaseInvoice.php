<?php

namespace App\Models\Invoices;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInvoice extends Invoice
{
    use HasFactory;

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
