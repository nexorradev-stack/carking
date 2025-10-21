<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function index()
    {
        $apiusers = User::where('source_type', 'api')->get();
        $clickdealers = User::where('source_type', 'ftp_feed')->get();
        return view('admin.api.index', compact('apiusers', 'clickdealers'));
    }

    public function connectDealer(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'dealer_id' => 'required|string|max:255',
            'source_type' => 'required|in:api,ftp_feed', 
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $user->update([
                'dealer_id' => $request->dealer_id,
                'source_type' => $request->source_type, 
            ]);
        }

        return redirect()->back()->with('success', 'Dealer connected successfully!');
    }
}


