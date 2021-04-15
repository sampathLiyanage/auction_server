<?php

namespace App\Http\Controllers;

use App\AutoBidStatus;
use App\Exceptions\BadRequestException;
use App\Exceptions\UnauthorizedException;
use App\Lib\AutoBidBot;
use App\Lib\AutoBidDefaultStrategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AutoBidStatusController extends Controller
{
    public function show() {
        $itemId = request('item_id');
        $userId = request('user_id');
        $validator = Validator::make(['item_id'=>$itemId, 'user_id'=>$userId],[
            'item_id' => 'required|integer|min:1',
            'user_id' => 'required|integer|min:1'
        ]);
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors()->toJson());
        } else if ($userId != Session::get('loggedInUserId')) {
            throw new UnauthorizedException();
        }
        $data = AutoBidStatus::firstOrCreate(['user_id'=>$userId, 'item_id'=>$itemId], []);
        return response()->json([
            'data' => $data,
        ], 200);
    }

    public function update(Request $request) {
        $params = $request->only('user_id', 'item_id', 'auto_bid_enabled');
        $validator = Validator::make($params,[
            'user_id' => 'required|integer|min:1',
            'item_id' => 'required|integer|min:1',
            'auto_bid_enabled' => 'required|boolean'
        ]);
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors()->toJson());
        }
        if ($params['user_id'] != Session::get('loggedInUserId')) {
            throw new UnauthorizedException();
        }
        $autoBidStatus = AutoBidStatus::updateOrCreate(['user_id'=>$params['user_id'], 'item_id'=>$params['item_id']], ['auto_bid_enabled'=>$params['auto_bid_enabled']]);
        if ($params['auto_bid_enabled']) {
            (new AutoBidBot(new AutoBidDefaultStrategy()))->autoBid($params['item_id']);
        }
        return response()->json([
            'data' => $autoBidStatus,
        ], 200);
    }
}
