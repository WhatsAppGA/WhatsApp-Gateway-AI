<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
class UserController extends Controller
{
   
    public function settings(){
        return view('pages.user.settings');
    }

    public function changePasswordPost(Request $request)
    {
        

        $request->validate([
            'current' => ['required', 'string' ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $newPassword = bcrypt($request->password);
        $request->user()->fill([
            'password' => $newPassword
        ])->save();

        return back()->with('alert', ['type' => 'success', 'msg' => 'Password Changed' ]);
    }

    public function generateNewApiKey(Request $request)
    {
        $newApiKey = Str::random(30);
        $request->user()->fill([
            'api_key' => $newApiKey
        ])->save();
        return back()->with('alert', [
            'type' => 'success',
            'msg' => 'Success set new Api Key'
        ]);
    }


}
