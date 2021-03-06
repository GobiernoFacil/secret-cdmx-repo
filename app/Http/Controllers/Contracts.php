<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Contract;

class Contracts extends Controller
{
	
	//
	// Contracts list
	//	
	public function index(){
		$contracts 			 = Contract::all();
		$data                = [];
		$data['title']       = 'Lista de Contrataciones Abiertas de la CDMX';
		$data['description'] = 'Lista de contratos abiertos de la Ciudad de México';
		$data['og_image']	 = "img/og/contrato-cdmx.png";
		$data['body_class']  = 'contract';
		
		$data['contracts']  = $contracts;
		
		return view("frontend.contracts.contracts_list")->with($data);
	}

    public function showAll(){
      $contracts = Contract::all();
      return view("frontend.contracts.show-all")->with(["contracts" => $contracts]);
    }
	
	
	//
	// Show Contract
	//
  public function show($ocid){
    // [1] Validate ocid & redirect if not valid
    $r = preg_match('/^[\w-]+$/', $ocid);
    if(!$r) return redirect("contratos");
	
	$contract = Contract::where("ocdsid", $ocid)->get()->first();
    if(!$contract) return redirect("contratos");
	$ocid	= $ocid;
	$con 	= $contract->releases->last(); 
    // [2] show the view
    	$data                = [];
		$data['title']       = $con->tender->title . " | Contrataciones Abiertas de la CDMX";
		$data['description'] = "Contrato: " . $con->tender->description;
		$data['og_image']	 = "img/og/contrato-cdmx.png";
		$data['body_class']  = 'contract single';
		$data['elcontrato']	 = $con;
		$data['ocid']	 	 = $ocid;
    
    return view("frontend.contracts.contract")->with($data);

  }

  //
  // [ SHOW RAW CONTRACT ]
  //
  //
  public function showRaw($ocid){
    // [1] Validate ocid & redirect if not valid
    $r = preg_match('/^[\w-]+$/', $ocid);
    if(!$r) return redirect("contratos");

    $base_contract = Contract::where("ocdsid", $ocid)->get()->first();

    if(!$base_contract) die(":(");

    // [2] make the call to the API
    $url  = 'http://187.141.34.209:9009/ocpcdmx/contratos';
    $data = ['dependencia' => $base_contract->cvedependencia, 'contrato' => $base_contract->ocdsid];

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
    echo "<pre>";
    var_dump($base_contract->toArray());
    echo "</pre>";

    echo "<pre>";
    var_dump($con);
    echo "</pre>";
    die();
    if(empty($result)) return redirect("contratos");

    // [4] show the view
      $data                = [];
    $data['title']       = $con->releases[0]->tender->title . " | Contrataciones Abiertas de la CDMX";
    $data['description'] = "Contrato: " . $con->releases[0]->tender->description;
    $data['og_image']  = "img/og/contrato-cdmx.png";
    $data['body_class']  = 'contract';
    $data['elcontrato']  = $con;
    
    return view("frontend.contracts.contract")->with($data);
  }
}
