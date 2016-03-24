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
		$og_image			 = "img/og/contrato-cdmx.png";
		$data['body_class']  = 'contract';
		
		//// lista de contratos aún sin implementar en el view
		$data['contracts']  = $contracts;
		
		return view("frontend.contracts_list")->with($data);
	}

  public function show($ocid){
    // [1] Validate ocid & redirect if not valid
    $r = preg_match('/^[\w-]+$/', $ocid);
    if(!$r) return redirect("contratos");

    // [2] make the call to the API
    $url  = 'http://187.141.34.209:9009/ocpcdmx/contratos';
    $data = ['dependencia' => '901', 'contrato' => $ocid];

    $ch   = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));
    $result = curl_exec($ch);
    $con    = json_decode($result);

    // [3] if the ocid is invalid, redirect
    if(empty($result)) return redirect("contratos");

    // [4] show the view
    return view("contract")->with(['con' => $con]);

  }
}
