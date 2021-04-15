<?php

namespace App\Http\Controllers;

use App\AutoBidStatus;
use App\Exceptions\BadRequestException;
use App\Exceptions\UnauthorizedException;
use App\Item;
use App\Lib\AutoBidBot;
use App\Lib\AutoBidDefaultStrategy;
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
            ->orderBy('id', 'DESC')
            ->get();
        return ['data'=>$data];
    }

    public function store(Request $request) {
        $params = $request->only('amount', 'user_id', 'item_id');
        $validator = Validator::make($params,[
            'amount' => 'nullable|integer|min:0',
            'user_id' => 'required|integer|min:1',
            'item_id' => 'required|integer|min:1'
        ]);
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors()->toJson());
        } else if ($params['user_id'] != Session::get('loggedInUserId')) {
            throw new UnauthorizedException();
        }
        $this->validateAutoBiddingNotEnabled($params['user_id'], $params['item_id']);
        $this->validateIfBiddingOnGoing($params['item_id']);
        if (!is_null($params['amount'])) {
            $this->validateBiddingAmount($params['item_id'], $params['amount']);
        }
        $bid = Bid::create($params);
        (new AutoBidBot(new AutoBidDefaultStrategy()))->autoBid($params['item_id']);
        return response()->json($bid, 201);
    }

    protected function validateIfBiddingOnGoing($itemId) {
        $item = Item::find($itemId);
        if (is_null($item->auction_end_time)) {
            throw new BadRequestException('Bidding is not started');
        } else if ($item->auction_end_time < date("Y-m-d H:i:s", time())) {
            throw new BadRequestException('Bidding is closed');
        }
    }

    protected function validateBiddingAmount($itemId, $biddingAmount) {
        $bidCount = Bid::where('item_id', '=', $itemId)
            ->orderBy('amount', 'DESC')
            ->count();
        if ($bidCount > 0) {
            $latestBid = Bid::where('item_id', '=', $itemId)
                ->orderBy('id', 'DESC')
                ->first();
            if ($biddingAmount <= $latestBid->amount) {
                throw new BadRequestException('Bid amount should be larger than previous bids');
            }
        }
    }

    protected function validateAutoBiddingNotEnabled($uerId, $itemId) {
        $autoBidStatus = AutoBidStatus::firstOrCreate(['user_id'=>$uerId, 'item_id'=>$itemId], []);
        if ($autoBidStatus->auto_bid_enabled) {
            throw new BadRequestException('Manual bidding is not allowed when auto bidding is enabled');
        }
    }
}
