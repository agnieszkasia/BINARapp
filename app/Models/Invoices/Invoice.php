<?php

namespace App\Models\Invoices;

use App\Models\InvoicesPositions\Product;
use App\Models\InvoicesPositions\Service;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'issue_date',
        'due_date',
        'gross',
        'net',
        'vat',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'invoice_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'invoice_id');
    }
}
