<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;

class SellersController extends Controller
{
   
    public function index()
    {
        $sellers = Seller::has('products')->get();
        return response()->json(['count' => $sellers->count(), 'data' => $sellers], 200);
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show($id)
    {
        $seller = Seller::has('products')->findOrFail($id);
        return response()->json(['data' => $seller], 200);
    }

    
    public function edit($id)
    {
        //
    }

    
    public function update(Request $request, $id)
    {
        //
    }

    
    public function destroy($id)
    {
        //
    }
}
