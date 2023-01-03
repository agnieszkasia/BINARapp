<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('sale_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->date('issue_date');
            $table->date('due_date');
            $table->float('gross');
            $table->float('net');
            $table->float('vat');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sale_invoices');
    }
}
