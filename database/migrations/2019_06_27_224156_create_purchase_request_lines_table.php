<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseRequestLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_request_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('purchase_request_id');
            $table->unsignedInteger('task_id');
            $table->unsignedInteger('supplier_id');
            $table->unsignedInteger('uom_id');
            $table->unsignedInteger('approver');
            $table->unsignedInteger('buyer');
            $table->string('item_number');
            $table->string('item_revision');
            $table->string('item_description');
            $table->float('qty_required');
            $table->float('qty_per_uom');
            $table->float('cost_per_uom');
            $table->dateTime('need_date');
            $table->longText('notes');
            $table->string('status');
            $table->string('po_number');

            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests');
            $table->foreign('task_id')->references('id')->on('tasks');
            $table->foreign('uom_id')->references('id')->on('uoms');
            $table->foreign('approver')->references('id')->on('users');
            $table->foreign('buyer')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_request_lines');
    }
}
