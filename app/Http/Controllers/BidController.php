<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bid;

class BidController extends Controller
{
    public function search() {
        $itemId = request('itemId', '');
        return Bid::where('item_id', '=', $itemId)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function store(Request $request) {
        $bid = Bid::create($request->all());
        return response()->json($bid, 201);
    }
}
