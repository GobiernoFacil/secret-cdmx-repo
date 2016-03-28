<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
  protected $fillable = ['ocdsid', 'ejercicio', 'cvedependencia', 'nomdependencia', 'published_date', 'uri', 'publisher_id'];

  public function publisher(){
    return $this->belongsTo('App\Models\Publisher');
  }

  public function releases(){
    return $this->hasMany('App\Models\Release');
  }
}
