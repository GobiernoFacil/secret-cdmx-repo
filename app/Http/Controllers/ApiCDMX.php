<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Contract;

class ApiCDMX extends Controller
{
  const PAGE_SIZE = 50;

  //
  // [ GET THE CONTRACT LIST ]
  //
  //
  public function listAll(Request $request, $page = 1){
    $page--;
    $total = Contract::all()->count();
    $contracts = Contract::skip($page * self::PAGE_SIZE)->take(self::PAGE_SIZE)->get();
    $response = [
      "contracts" => $contracts,
      "total"     => $total,
      "page"      => $page+1,
      "page_size" => self::PAGE_SIZE
    ];
    return response()->json($response)->header('Access-Control-Allow-Origin', '*');
  }
}
