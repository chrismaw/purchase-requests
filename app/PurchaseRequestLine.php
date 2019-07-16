<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequestLine extends Model
{
    public $timestamps = false;

    const PRL_STATUSES = [
        'Pending Approval', 'Unreleased Drawing', 'Approved for Purchasing',
        'PO in Progress', 'PO Revision', 'Order Complete', 'Request Cancelled'
    ];

    public function purchaseRequest(){
        return $this->belongsTo('App\PurchaseRequest');
    }

    public function task(){
        return $this->belongsTo('App\Task');
    }

    public function supplier(){
        return $this->belongsTo('App\Supplier');
    }

    public function uom(){
        return $this->belongsTo('App\Uom');
    }

    public function approverUser(){
        return $this->belongsTo('App\User','approver');
    }

    public function buyerUser(){
        return $this->belongsTo('App\User','buyer');
    }

}
