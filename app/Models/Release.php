<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Release extends Model
{
  protected $fillable = ["local_id", "contract_id", "ocid", "date", 
  "initiation_type", "planning_id", "buyer_id", "tender_id", "language"];

  public function contract(){
    return $this->belongsTo('App\Models\Contract');
  }

  public function planning(){
    return $this->hasOne('App\Models\Planning');
  }

  public function tender(){
    return $this->hasOne('App\Models\Tender');
  }
}
