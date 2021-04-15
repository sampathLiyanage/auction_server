<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Exceptions\UnauthorizedException;
use App\Http\Controllers\Controller;
use App\Configuration;
use App\Lib\AutoBidBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ConfigurationController extends Controller
{
    public function show($userId) {
        $this->validateUserId($userId);
        if ($userId != Session::get('loggedInUserId')) {
            throw new UnauthorizedException();
        }
        return Configuration::firstOrCreate(['user_id'=>$userId], ['max_bid_amount'=>null]);
    }

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
            AutoBidBot::getInstance()->handleAutoBid(null, null);
        }
        return response()->json($configuration, 200);
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
