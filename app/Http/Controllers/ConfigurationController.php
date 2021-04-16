<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Exceptions\UnauthorizedException;
use App\Configuration;
use App\Lib\AutoBidBot;
use App\Lib\AutoBidDefaultStrategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ConfigurationController extends Controller
{
    /**
     * @api {get} api/configurations/:userId Get configurations of a user
     * @apiName GetConfiguration
     * @apiGroup Auction
     *
     * @apiParam {Number} userId Id of the user
     *
     * @apiSuccess {Json} matching configuration as data
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     * {
     *  "data": {
     *    "id": 1,
     *    "user_id": 1,
     *    "max_bid_amount": 501,
     *    "created_at": "2021-04-15T17:44:30.000000Z",
     *    "updated_at": "2021-04-15T22:55:03.000000Z"
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
    public function show($userId) {
        $this->validateUserId($userId);
        if ($userId != Session::get('loggedInUserId')) {
            throw new UnauthorizedException();
        }
        $data = Configuration::firstOrCreate(['user_id'=>$userId], ['max_bid_amount'=>null]);
        return response()->json(['data'=>$data], 200);
    }

    /**
     * @api {patch} api/configuration/:userId Update user configuration
     * @apiName UpdateConfiguration
     * @apiGroup Auction
     *
     * @apiParam {Number} userId Id of the user
     * @apiParam {Boolean} max_bid_amount Maximum auto bid amount
     *
     * @apiSuccess {Json} matching configuration as data
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     * {
     *  "data": {
     *    "id": 1,
     *    "user_id": 1,
     *    "max_bid_amount": 501,
     *    "created_at": "2021-04-15T17:44:30.000000Z",
     *    "updated_at": "2021-04-15T22:55:03.000000Z"
     *  }
     * }
     *
     * @apiError ValidationError validation error
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "error": "max_bid_amount field should be a number"
     *     }
     *
     * @apiError Unauthorized Unauthorized error
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 403 Unauthorized
     *     {}
     */
    public function update(Request $request, $userId) {
        $this->validateUserId($userId);
        $params = $request->only('max_bid_amount');
        $validator = Validator::make($params,[
            'max_bid_amount' => 'nullable|numeric|min:0'
        ]);
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors()->toJson());
        }
        if ($userId != Session::get('loggedInUserId')) {
            throw new UnauthorizedException();
        }
        $configuration = Configuration::updateOrCreate(['user_id'=>$userId], ['max_bid_amount'=>$params['max_bid_amount']]);
        if ($params['max_bid_amount'] > 0) {
            (new AutoBidBot(new AutoBidDefaultStrategy()))->autoBid(null, null);
        }
        return response()->json(['data'=>$configuration], 200);
    }

    protected function validateUserId($userId) {
        $validator = Validator::make(['userId'=>$userId],[
            'userId' => 'required|integer|min:1'
        ]);
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors()->toJson());
        }
    }
}
