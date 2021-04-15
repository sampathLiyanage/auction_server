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
    public function show($userId) {
        $this->validateUserId($userId);
        if ($userId != Session::get('loggedInUserId')) {
            throw new UnauthorizedException();
        }
        $data = Configuration::firstOrCreate(['user_id'=>$userId], ['max_bid_amount'=>null]);
        return response()->json(['data'=>$data], 200);
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
