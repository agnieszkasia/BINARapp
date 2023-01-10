<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorrectiveInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corrective_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->date('issue_date');
            $table->date('due_date');
            $table->float('gross');
            $table->float('net');
            $table->float('vat');
            $table->integer('company_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corrective_invoices');
    }
}
