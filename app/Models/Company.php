<?php

namespace App\Models;

use App\Models\Invoices\SaleInvoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'address2',
        'nip'
    ];

    public function saleInvoices(): HasMany
    {
        return $this->hasMany(SaleInvoice::class);
    }
}
