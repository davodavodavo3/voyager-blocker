<?php

namespace VoyagerBlocker\Http\Controllers;

use Illuminate\Http\Request;
use VoyagerBlocker\Models\VoyagerBlocker;
use Exception;

class VoyagerBlockerController extends \App\Http\Controllers\Controller
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

    public function browse()
    {
        $blocker = VoyagerBlocker::first();

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
    public function update(Request $request)
    {
        $request->ips = json_decode(json_encode($request->ips), false);

        $blocker = VoyagerBlocker::updateOrCreate(
            ['id' => 5],
            ['ips' => $request->ips]
        );


        return redirect()->back()->with(
            [
                'alert-type' => 'success',
                'message' => 'Blocker successfully updated!',
                'blocker' => $blocker
            ]
        );
    }
}
