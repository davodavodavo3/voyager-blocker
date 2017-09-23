<?php

namespace VoyagerBlocker\Http\Controllers;

use Illuminate\Http\Request;
use VoyagerBlocker\Models\Blocker;

class BlockerController extends \App\Http\Controllers\Controller
{
    //***************************************
    //               ____
    //              |  _ \
    //              | |_) |
    //              |  _ <
    //              | |_) |
    //              |____/
    //
    //          Browse the blocker
    //
    //****************************************

    public function browse(){
    	$blocker = Blocker::first();
    	return view('blocker::browse', compact('blocker'));
    }

    //***************************************
    //               _    _
    //              | |  | |
    //              | |  | |
    //              | |__| |
    //              |______|
    //               
    //          Update a blocker
    //
    //****************************************

    // PUT REQUEST
    public function update(Request $request){
		try{
            $request->ips = json_decode(json_encode($request->ips), FALSE);
        
            $blocker = Blocker::updateOrCreate(
                    ['ips' => $request->ips]
                );
        } catch(Exception $e){
            return response()->json( ['status' => 'error', 'message' => $e->getMessage] );
        }
		
		return response()->json( ['status' => 'success', 'message' => 'Blocker successfully updated!', 'blocker' => $blocker] );
    }
}
