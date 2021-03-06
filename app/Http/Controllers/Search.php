<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

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

class Search extends Controller
{
  public function index(Request $request){
    $query = $request->input('query', false);

    if($query){
      $contracts = Contract::where('nomdependencia', 'like', '%' . $query.'%')
      ->orWhere('ejercicio', 'like', '%' . $query . '%')
      ->orWhere('ocdsid', 'like', '%' . $query . '%')
      // SEARCH ON PUBLISHER
      ->orWhere(function($q) use($query){
        $q->whereHas('publisher', function($q) use($query){
          $q->where('name', 'like', '%' . $query . '%');
        });
      })

      // SEARCH ON PLANNINGS
      ->orWhere(function($q) use($query){
        $q->whereHas('plannings', function($q) use($query){
          $q->where('project', 'like', '%' . $query . '%');
        });
      })

      // SEARCH BUYER
      ->orWhere(function($q) use($query){
        $q->whereHas('releases', function($q) use($query){
          $q->whereHas('buyer', function($q) use($query){
            $q->where('name', 'like', '%' . $query . '%');
          });
        });
      })

      // SEARCH ON TENDERS
      ->orWhere(function($q) use($query){
        $q->whereHas('tenders', function($q) use($query){
          $q->where('description', 'like', '%' . $query . '%');
        })
        ->orWhereHas('tenders', function($q) use($query){
          $q->where('title', 'like', '%' . $query . '%');
        })
        ->orWhereHas('tenders', function($q) use($query){
          $q->where('status', 'like', '%' . $query . '%');
        });
      })

      // SEARCH ON AWARDS
      ->orWhere(function($q) use($query){
        $q->whereHas('awards', function($q) use($query){
          $q->where('description', 'like', '%' . $query . '%');
        })
        ->orWhereHas('awards', function($q) use($query){
          $q->where('title', 'like', '%' . $query . '%');
        });
      })

      ->get();
    }
    else{
      $contracts = null;
    }

    return view('frontend.search')->with(['contracts' => $contracts]);
    //
  }
}
