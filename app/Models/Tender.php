<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
  protected $fillable = ["release_id"];
    //
  public function items(){
    return $this->hasMany('App\Models\Item');
  }
}
