<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
  public function release(){
    return $this->belongsTo('App\Models\Release');
  }
    //
}
