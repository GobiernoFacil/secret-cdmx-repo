<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class Frontend extends Controller
{
	//
	//
	//HOME
	//
	public function index(){
		$data                = [];
    	$data['title']       = 'Contrataciones Abiertas de la CDMX';
    	$data['description'] = 'Contrataciones Abiertas de la Ciudad de México';
		$og_image			 = "img/og/contrataciones-abiertas-cdmx.png";
    	$data['body_class']  = 'home';
    	return view("frontend.home")->with($data);
	}

  
}
