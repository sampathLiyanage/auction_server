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
    /**
     * @api {get} api/autoBidStatus?itemId=:itemId&userId=:userId Get auto bid status
     * @apiName GetAutoBidStatus
     * @apiGroup Auction
     *
     * @apiParam {Number} itemId Id of an action item
     * @apiParam {Number} userId Id of a user
     *
     * @apiSuccess {Json} matching auction item as data
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     * {
     *  "data": {
     *    "id": 13,
     *    "user_id": 1,
     *    "item_id": 22,
     *    "auto_bid_enabled": 0,
     *    "created_at": "2021-04-15T23:51:20.000000Z",
     *    "updated_at": "2021-04-15T23:51:20.000000Z"
     *  }
     * }
     *
     * @apiError ValidationError validation error
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "error": "id field should be an integer"
     *     }
     *
     * @apiError Unauthorized Unauthorized error
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 403 Unauthorized
     *     {}
     */
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

    /**
     * @api {patch} api/autoBidStatus?itemId=:itemId&userId=:userId Update auto bid status
     * @apiName UpdateAutoBidStatus
     * @apiGroup Auction
     *
     * @apiParam {Number} itemId Id of an action item
     * @apiParam {Number} userId Id of a user
     * @apiParam {Boolean} auto_bid_enabled Auto bid enabled or not
     *
     * @apiSuccess {Json} matching auction item as data
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     * {
     *  "data": {
     *    "id": 13,
     *    "user_id": 1,
     *    "item_id": 22,
     *    "auto_bid_enabled": true,
     *    "created_at": "2021-04-15T23:51:20.000000Z",
     *    "updated_at": "2021-04-16T00:16:54.000000Z"
     *  }
     * }
     *
     * @apiError ValidationError validation error
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "error": "user_id field should be an integer"
     *     }
     *
     * @apiError Unauthorized Unauthorized error
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 403 Unauthorized
     *     {}
     */
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
