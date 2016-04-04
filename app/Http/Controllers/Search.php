<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Award;
use App\Models\Buyer;
use App\Models\Contract;
use App\Models\Item;
use App\Models\Planning;
use App\Models\Provider;
use App\Models\Publisher;
use App\Models\Release;
use App\Models\SingleContract;
use App\Models\Supplier;
use App\Models\Tender;
use App\Models\Tenderer;

class Search extends Controller
{
  public function index(Request $request){
    $query = $request->input('query', false);

    if($query){
      $contracts = Contract::where('nomdependencia', 'like', '%' . $query.'%')
      ->orWhere('ejercicio', 'like', '%' . $query . '%')
      //->orWhere
      ->get();
    }
    else{
      $contracts = null;
    }

    return view('frontend.search')->with(['contracts' => $contracts]);
    //
  }
}
