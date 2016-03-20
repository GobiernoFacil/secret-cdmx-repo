<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class Contracts extends Controller
{
  public function index(){
    echo ":D";
  }

  public function show($ocid){
    // [1] Validate ocid & redirect if not valid
    $r = preg_match('/^[\w-]+$/', $ocid);
    if(!$r) return redirect("contratos");

    // [2] make the call to the API
    $url  = 'http://187.141.34.209:9009/ocpcdmx/contratos';
    $data = ['dependencia' => '901', 'contrato' => $ocid];

    // [2.1] the CURL stuff
    $ch   = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
    $result = curl_exec($ch);
    $con    = json_decode($result);

    // [3] if the ocid is invalid, redirect
    var_dump($result);
    die();
    if(empty($result)) return redirect("contratos");

    // [4] show the view
    return view("contract")->with(['con' => $con]);

  }
}
