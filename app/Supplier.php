<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    public $timestamps = false;

    public function createdByUser(){
        return $this->belongsTo('App\User','created_by');
    }
}
