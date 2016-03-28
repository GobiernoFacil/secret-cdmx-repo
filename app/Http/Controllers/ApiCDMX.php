<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Contract;

class ApiCDMX extends Controller
{
  public function listAll(Request $request){
    $contracts = Contract::all();
    return response()->json($contracts)->header('Access-Control-Allow-Origin', '*');
  }
}
