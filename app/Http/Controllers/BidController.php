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
    /**
     * @api {get} api/bids?itemId=:itemId Get bids of an auction item
     * @apiName GetBids
     * @apiGroup Auction
     *
     * @apiParam {Number} itemId id of an auction item
     *
     * @apiSuccess {Json} matching bids
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     * {
     *  "data": [
     *    {
     *      "id": 55,
     *      "amount": 1001,
     *      "user_id": 1,
     *      "item_id": 2,
     *      "is_auto_bid": 1,
     *      "created_at": "2021-04-15T20:51:53.000000Z",
     *      "updated_at": "2021-04-15T20:51:53.000000Z",
     *      "user": {
     *        "id": 1,
     *        "name": "user1"
     *      }
     *    },
     *    {
     *      "id": 54,
     *      "amount": 530,
     *      "user_id": 2,
     *      "item_id": 2,
     *      "is_auto_bid": 1,
     *      "created_at": "2021-04-15T20:51:53.000000Z",
     *      "updated_at": "2021-04-15T20:51:53.000000Z",
     *      "user": {
     *        "id": 2,
     *        "name": "user2"
     *      }
     *    },
     *    {
     *      "id": 51,
     *      "amount": 529,
     *      "user_id": 1,
     *      "item_id": 2,
     *      "is_auto_bid": 1,
     *      "created_at": "2021-04-15T20:09:29.000000Z",
     *      "updated_at": "2021-04-15T20:09:29.000000Z",
     *      "user": {
     *        "id": 1,
     *        "name": "user1"
     *      }
     *    }
     *  ]
     * }
     *
     * @apiError ValidationError validation error
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "error": "item_id is required"
     *     }
     */
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
        return response()->json([
            'data' => $data,
        ], 200);
    }

    /**
     * @api {post} api/bids Place bids on auction items
     * @apiName CreateBids
     * @apiGroup Auction
     *
     * @apiParam {String} amount bid amount
     * @apiParam {String} user_id id of the user submitting the bid
     * @apiParam {String} item_id id of the auction item which the bid is on
     *
     * @apiSuccess {Json} placed bid data
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 201 Created
     * {
     *  "data": {
     *    "amount": 234,
     *    "user_id": "1",
     *    "item_id": 22,
     *    "updated_at": "2021-04-15T23:51:23.000000Z",
     *    "created_at": "2021-04-15T23:51:23.000000Z",
     *    "id": 59
     *  }
     * }
     *
     * @apiError ValidationError validation error
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Bid amount should be larger than previous bids"
     *     }
     *
     * @apiError Unauthorized Unauthorized error
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 403 Unauthorized
     *     {}
     */
    public function store(Request $request) {
        $params = $request->only('amount', 'user_id', 'item_id');
        $validator = Validator::make($params,[
            'amount' => 'required|integer|min:0',
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
        $this->validateBiddingAmountAndLatestBid($params['item_id'], $params['user_id'], $params['amount']);
        $bid = Bid::create($params);
        (new AutoBidBot(new AutoBidDefaultStrategy()))->autoBid($params['item_id']);
        return response()->json([
            'data' => $bid,
        ], 201);
    }

    protected function validateIfBiddingOnGoing($itemId) {
        $item = Item::find($itemId);
        if (is_null($item->auction_end_time)) {
            throw new BadRequestException('Bidding is not started');
        } else if ($item->auction_end_time < date("Y-m-d H:i:s", time())) {
            throw new BadRequestException('Bidding is closed');
        }
    }

    protected function validateBiddingAmountAndLatestBid($itemId, $userId, $biddingAmount) {
        $bidCount = Bid::where('item_id', '=', $itemId)
            ->orderBy('amount', 'DESC')
            ->count();
        if ($bidCount > 0) {
            $latestBid = Bid::where('item_id', '=', $itemId)
                ->orderBy('id', 'DESC')
                ->first();
            if ($biddingAmount <= $latestBid->amount) {
                throw new BadRequestException('Bid amount should be larger than previous bids');
            } else if ($userId == $latestBid->user_id) {
                throw new BadRequestException('Highest bid is already from the same user');
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
