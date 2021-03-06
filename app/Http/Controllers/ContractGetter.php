<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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

          if(count($r->awards)){
            foreach($r->awards as $aw){
              $award = Award::firstOrCreate([
                "release_id" => $release->id,
                "local_id"   => $aw->id
              ]);

              $award->title       = $aw->title;
              $award->description = $aw->description;
              $award->status      = $aw->status;
              $award->date        = date("Y-m-d", strtotime($aw->date));
              $award->value       = $aw->value->amount;
              $award->currency    = $aw->value->currency;

              $award->update();

              if(count($aw->items)){
                foreach($aw->items as $it){
                  $item = $award->items()->firstOrCreate([
                    'local_id'  => $it->id
                  ]);

                  $item->quantity    = $it->quantity;
                  $item->description = $it->description;
                  $item->unit        = $it->unit->name;

                  $item->update();
                }
              }

              if(count($aw->suppliers)){
                foreach($aw->suppliers as $sup){
                  $supplier = Supplier::firstOrCreate([
                    "award_id" => $aw->id,
                    "rfc"      => $sup->identifier->id
                  ]);

                  $supplier->name         = $sup->name;
                  $supplier->street       = $sup->address->streetAddress;
                  $supplier->locality     = $sup->address->locality;
                  $supplier->region       = $sup->address->region;
                  $supplier->zip          = $sup->address->postalCode;
                  $supplier->country      = $sup->address->countryName;
                  $supplier->contact_name = $sup->contactPoint->name;
                  $supplier->email        = $sup->contactPoint->email;
                  $supplier->phone        = $sup->contactPoint->telephone;
                  $supplier->fax          = $sup->contactPoint->faxNumber;
                  $supplier->url          = $sup->contactPoint->url;

                  $supplier->update();
                }
              }
            }
          }

          if(count($r->contracts)){
              foreach($r->contracts as $single){
                $single_contract = SingleContract::firstOrCreate([
                  "local_id"   => $single->id,
                  "release_id" => $release->id
                ]);

                $single_contract->award_id = $single->awardID;
                $single_contract->title = $single->title;
                $single_contract->description = $single->description;
                $single_contract->status = $single->status;
                $single_contract->contract_start = $single->period ? date("Y-m-d", strtotime($single->period->startDate)) : null;
                $single_contract->contract_end = $single->period ? date("Y-m-d", strtotime($single->period->endDate)) : null;
                $single_contract->amount = $single->value->amount;
                $single_contract->currency = $single->value->currency;
                $single_contract->date_signed = $single->dateSigned ? date("Y-m-d", strtotime($single->dateSigned)) : null;
                $single_contract->documents = count($single->documents);// ? implode(',',$r->tender->submissionMethod) : null;

                $single_contract->update();

                if(count($single->items)){
                  foreach($single->items as $it){
                    $item = $single_contract->items()->firstOrCreate([
                      'local_id'  => $it->id
                    ]);

                    $item->quantity    = $it->quantity;
                    $item->description = $it->description;
                    $item->unit        = $it->unit->name;

                    $item->update();
                  }
                }
              }
            }

          // create planning
          if($r->planning){
            $planning = Planning::firstOrCreate([
              "release_id" => $release->id
            ]);

            $planning->amount   = $r->planning->budget->amount->amount;
            $planning->currency = $r->planning->budget->amount->currency;
            $planning->project  = $r->planning->budget->project;

            $planning->update();

            //$release->planning_id = $planning->id;
          }
          // create tender
          if($r->tender){
            $tender = Tender::firstOrCreate([
              "release_id" => $release->id
            ]);

            $tender->local_id             = $r->tender->id;
            $tender->title                = $r->tender->title;
            $tender->description          = $r->tender->description;
            $tender->status               = $r->tender->status;
            $tender->amount               = $r->tender->value ? $r->tender->value->amount : null;
            $tender->currency             = $r->tender->value ? $r->tender->value->currency : null;
            $tender->procurement_method   = $r->tender->procurementMethod;
            $tender->award_criteria       = $r->tender->awardCriteria;
            $tender->tender_start         = $r->tender->tenderPeriod ? date("Y-m-d", strtotime($r->tender->tenderPeriod->startDate)) : null;
            $tender->tender_end           = $r->tender->tenderPeriod ? date("Y-m-d", strtotime($r->tender->tenderPeriod->endDate)) : null;
            $tender->enquiry_start        = $r->tender->enquiryPeriod ? date("Y-m-d", strtotime($r->tender->enquiryPeriod->startDate)) : null;
            $tender->enquiry_end          = $r->tender->enquiryPeriod ? date("Y-m-d", strtotime($r->tender->enquiryPeriod->endDate)) : null;
            $tender->award_start          = $r->tender->awardPeriod ? date("Y-m-d", strtotime($r->tender->awardPeriod->startDate)) : null;
            $tender->award_end            = $r->tender->awardPeriod ? date("Y-m-d", strtotime($r->tender->awardPeriod->endDate)) : null;
            $tender->has_enquiries        = $r->tender->hasEnquiries;
            $tender->eligibility_criteria = $r->tender->eligibilityCriteria;
            $tender->submission_method    = count($r->tender->submissionMethod) ? implode(',',$r->tender->submissionMethod) : null; 
            $tender->number_of_tenderers  = $r->tender->numberOfTenderers;

            $tender->update();


            if(count($r->tender->tenderers)){
              foreach($r->tender->tenderers as $tn){
                $tenderer = Tenderer::firstOrCreate([
                  "rfc" => $tn->identifier->id
                ]);

                $tenderer->name         = $tn->name;
                $tenderer->street       = $tn->address->streetAddress;
                $tenderer->locality     = $tn->address->locality;
                $tenderer->region       = $tn->address->region;
                $tenderer->zip          = $tn->address->postalCode;
                $tenderer->country      = $tn->address->countryName;
                $tenderer->contact_name = $tn->contactPoint->name;
                $tenderer->email        = $tn->contactPoint->email;
                $tenderer->phone        = $tn->contactPoint->telephone;
                $tenderer->fax          = $tn->contactPoint->faxNumber;
                $tenderer->url          = $tn->contactPoint->url;

                $tenderer->update();
              }
            }


            if(count($r->tender->items)){
              foreach($r->tender->items as $it){
                $item = $tender->items()->firstOrCreate([
                  'local_id'  => $it->id
                ]);

                $item->quantity    = $it->quantity;
                $item->description = $it->description;
                $item->unit        = $it->unit->name;

                $item->update();
              }
            }
          }

          // create buyer
          if($r->buyer){
            $buyer = Buyer::firstOrCreate([
              "local_id" => $r->buyer->identifier->id,
              "name"     => $r->buyer->name
            ]);

            $buyer->uri = $r->buyer->identifier->uri;
            $buyer->update();

            $release->buyer_id = $buyer->id;
            $release->update();
          }
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

  public function getJSON($ocid){
    // [1] Validate ocid & redirect if not valid
    $r = preg_match('/^[\w-]+$/', $ocid);
    if(!$r) die("O_______O");

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
    //$con    = json_decode($result);

    //echo "<pre>";
    //var_dump($con);
    //echo "</pre>";
    $file = $ocid . ".json";
    //file_put_contents($file, $result);
    //return response()->download($file);
    header('Content-Disposition: attachment; filename="' . $file . '"');
    header('Content-Type: application/json');
    header('Content-Length: ' . strlen($result));
    header('Connection: close');

    echo $result;
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
