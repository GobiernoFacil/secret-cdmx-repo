<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Contract;
use App\Models\Planning;
use App\Models\Provider;
use App\Models\Publisher;
use App\Models\Release;

class ContractGetter extends Controller
{
  public $apiContratos   = 'http://187.141.34.209:9009/ocpcdmx/listarcontratos';
  public $apiContrato    = 'http://187.141.34.209:9009/ocpcdmx/contratos';
  public $apiProveedores = 'http://10.1.65.84:9000/ocpcdmx/cproveedores';
  public $apiProveedores2 = 'http://187.141.34.209:9009/ocpcdmx/cproveedores';
  public $dependencia    = 901;

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

      // save extra data 
      $meta = $this->getMetaDataX($contract);
      if(!empty($meta) && ! property_exists($meta, 'error')){
        // add extra data to contracts
        $contract->uri = $meta->uri;
        $contract->published_date = date("Y-m-d", strtotime($meta->publishedDate));
        // create the publisher
        $contract->publisher_id = Publisher::firstOrCreate([
          "scheme" => $meta->publisher->scheme,
          "name"   => $meta->publisher->name,
          "uri"    => $meta->publisher->uri,
          "uid"    => $meta->publisher->uid
        ]);
        // update contract
        $contract->update();

        // create releases
        foreach($meta->releases as $r){
          $release = Release::firstOrCreate([
            "local_id"        => $r->id,
            "contract_id"     => $contract->id,
            "ocid"            => $contract->ocdsid,
            "date"            => date("Y-m-d", strtotime($r->date)),
            "initiation_type" => $r->initiationType,
            "language"        => $r->language
          ]);

          // create planning
          if($release->planning){
            $planning = Planning::firstOrCreate([
              "release_id" => $release->id
            ]);

            $planning->amount   = $r->planning->budget->amount->amount;
            $planning->currency = $r->planning->budget->amount->currency;
            $planning->project  = $r->planning->budget->project;

            $planning->update();
          }
          /*
| local_id        | int(11)          | NO   |     | NULL    |                |
| contract_id     | int(11)          | NO   |     | NULL    |                |
| ocid            | varchar(255)     | NO   |     | NULL    |                |
| date            | date             | YES  |     | NULL    |                |
| initiation_type | varchar(255)     | YES  |     | NULL    |                |
| planning_id     | int(11)          | YES  |     | NULL    |                |
| buyer_id        | int(11)          | YES  |     | NULL    |                |
| tender_id       | int(11)          | YES  |     | NULL    |                |
| language        | varchar(255)     | YES  |     | NULL    |                |
          */
        }
      }
    }
    
    echo ":D";
  }

  private function getMetaDataX($contract){
    // [1] make the call to the API
    $url  = $this->apiContrato;
    $data = ['dependencia' => $contract->cvedependencia, 'contrato' => $contract->ocdsid];

    // [1.1] the CURL stuff
    $ch   = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));
    $result = curl_exec($ch);
    $r      = json_decode($result);
    return $r;
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

    echo "<pre>";
    var_dump($con);
    echo "</pre>";
  }

  public function updateContracts(){
    $contracts = Contract::all();

    foreach($contracts as $contract){
      $data = ['dependencia' => '901', 'contrato' => $contract->ocdsid];
      // [2.1] the CURL stuff
      $ch   = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_URL, $this->apiContrato);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));
      $result = curl_exec($ch);
      $con    = json_decode($result);

      echo "<pre>";
      var_dump($con);
      echo "</pre>";
    }
  }

  public function getProviders($from, $to){
    // [2] make the call to the API
    $data = ['rangoInicio' => "2", 'rangoFinal' => "4"];

    // [2.1] the CURL stuff
    $ch   = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $this->apiProveedores2);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));
    $result = curl_exec($ch);
    $con    = json_decode($result);

    echo "<pre>";
    var_dump($con);
    echo "</pre>";
  }

  public function saveProviders($from = 1){
    $from = 34270;
    $step = 100;
    $max_steps = 100;
    $walk = true;

    while($walk){
      // [2] make the call to the API
      $data = ['rangoInicio' => $from, 'rangoFinal' => $from+$step];

      // [2.1] the CURL stuff
      $ch   = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_URL, $this->apiProveedores2);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));
      $result = curl_exec($ch);
      $con    = json_decode($result);

      forEach($con as $p){
        $provider = Provider::firstOrCreate(['rfc' => $p->rfc, 'name' => $p->name]);
      
        $provider->total = $p->total;
        // address
        $provider->street   = $p->address->streetAddress;
        $provider->locality = $p->address->locality;
        $provider->region   = $p->address->region;
        $provider->zip      = $p->address->postalCode;
        $provider->country  = $p->address->countryName;
        // contactPoint
        $provider->contact_name = $p->contactPoint->name;
        $provider->email = $p->contactPoint->email;
        $provider->phone = $p->contactPoint->telephone;
        $provider->fax = $p->contactPoint->faxNumber;
        $provider->url = $p->contactPoint->url;
        $provider->update();
      }
      echo $from . "<br>";
      $max_steps--;
      $from += $step;
      if($max_steps < 1) $walk = false;
    }
  }
}
