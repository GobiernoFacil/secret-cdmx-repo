<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Contract;

class ContractGetter extends Controller
{
  public $apiContratos = 'http://187.141.34.209:9009/ocpcdmx/listarcontratos';
  public $dependencia  = 901;

  public function getList(){
    $d = [];
    // GET LAST THREE YEARS OF DATA
    for($i = 0; $i < 3; $i ++){
      $year = date("Y") - $i;
      $data = ['dependencia' => '901', "ejercicio" => $year];
      $ch   = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_URL, $this->apiContratos );
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));
      $result    = curl_exec($ch);
      $excercise = json_decode($result);
      $d = array_merge($d, $excercise);
    }

    // SAVE THEM TO THE DB
    forEach($d as $c){
      $contract = Contract::firstOrCreate([
        'ocdsid'         => $c->ocdsID, 
        'ejercicio'      => $c->ejercicio, 
        'cvedependencia' => $c->cveDependencia, 
        'nomDependencia' => $c->nomDependencia
      ]);
    }
    
    //header("Content-Type: application/json");
    //echo $result;
    var_dump($d);
  }

  public function getMetaData($ocid){
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
    $result = curl_exec($ch);
    $con    = json_decode($result);

    var_dump($con);
  }
}
