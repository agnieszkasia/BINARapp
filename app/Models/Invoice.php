<?php

namespace App\Models;

use App\Models\InvoicesPostions\Product;
use App\Models\InvoicesPostions\Service;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function seller(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'invoice_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'invoice_id');
    }
}
