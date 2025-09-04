<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $data = Client::all();

        return response()->json(['message' => 'Client list', 'data' => $data], 200);
    }
}
