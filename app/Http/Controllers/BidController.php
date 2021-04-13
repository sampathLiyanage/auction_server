<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Exceptions\UnauthorizedException;
use Illuminate\Http\Request;
use App\Bid;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BidController extends Controller
{
    public function search() {
        $itemId = request('itemId');
        $validator = Validator::make(['itemId'=>$itemId],[
            'itemId' => 'required|integer|min:1'
        ]);
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors()->toJson());
        }
        $data = Bid::with('user:id,name')
            ->where('item_id', '=', $itemId)
            ->orderBy('created_at', 'DESC')
            ->get();
        return ['data'=>$data];
    }

    public function store(Request $request) {
        $params = $request->only('amount', 'user_id', 'item_id', 'is_auto_bid');
        $validator = Validator::make($params,[
            'amount' => 'required|numeric|min:0',
            'user_id' => 'required|integer|min:1',
            'item_id' => 'required|integer|min:1',
            'is_auto_bid' => 'boolean',
        ]);
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors()->toJson());
        } else if ($params['user_id'] != Session::get('loggedInUserId')) {
            throw new UnauthorizedException();
        }
        $bid = Bid::create($params);
        return response()->json($bid, 201);
    }
}
