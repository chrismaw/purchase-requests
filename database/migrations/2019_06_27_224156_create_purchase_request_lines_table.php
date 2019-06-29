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
            $table->unsignedInteger('supplier_id')->nullable();
            $table->unsignedInteger('uom_id')->default(1);
            $table->unsignedInteger('approver')->nullable();
            $table->unsignedInteger('buyer')->nullable();
            $table->string('item_number')->nullable();
            $table->string('item_revision')->nullable();
            $table->string('item_description');
            $table->float('qty_required');
            $table->float('qty_per_uom')->default('1.00');
            $table->float('cost_per_uom')->default('0.00');
            $table->float('uom_qty_required')->nullable();
            $table->dateTime('need_date');
            $table->longText('notes')->nullable();
            $table->string('status')->default('Pending Approval');
            $table->string('po_number')->nullable();

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
