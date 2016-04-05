<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Contract;

class Suppliers extends Controller
{
	
	//
	// Suppliers list
	//	
	public function index(){
		
	}

	//
	// Supplier 
	//
	public function show($id){
		$contracts 			 = Contract::all();
		$data                = [];
		$data['title']       = 'Proveedor';
		$data['description'] = 'Proveedor';
		$data['og_image']	 = "img/og/contrato-cdmx.png";
		$data['body_class']  = 'proveedor';
		
		$data['contracts']  = $contracts;
		
		return view("frontend.supplier")->with($data);
	}

}
