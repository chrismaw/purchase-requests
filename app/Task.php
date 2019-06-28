<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public $timestamps = false;

    public function createdByUser(){
        return $this->belongsTo('App\User','created_by');
    }
}
