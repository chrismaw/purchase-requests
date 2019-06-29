<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    public function project(){
        return $this->belongsTo('App\Project');
    }

    public function requestedByUser(){
        return $this->belongsTo('App\User','requester');
    }

}
