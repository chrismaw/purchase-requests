<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PurchaseRequest extends Model
{

    const PR_STATUSES = [
        'Open', 'On Hold', 'Closed'
    ];

    public function project(){
        return $this->belongsTo('App\Project');
    }

    public function requestedByUser(){
        return $this->belongsTo('App\User','requester');
    }

    public function projectRequestLines(){
        return $this->hasMany('App\ProjectRequestLine');
    }

    public function active(){
        return !$this->is_deleted;
    }

    public function scopeActive(Builder $query){
        return $query->where('is_deleted', false);
    }

}
