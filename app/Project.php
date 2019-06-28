<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public $timestamps = false;

    public function tasks(){
        return $this->hasMany(Task::class);
    }
}
