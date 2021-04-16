<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Item;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    /**
     * @api {get} api/items?filter=:filter&offset=:offset&limit=:limit&sortField=:sortField&sortOrder=:sortOrder Get Auction Items
     * @apiName GetAuctionItems
     * @apiGroup Auction
     *
     * @apiParam {String} filter string to match items by name or description
     * @apiParam {number} offset offset of the results
     * @apiParam {number} limit no of items expected to be returned
     * @apiParam {String} sortField sort field. should be one of name, price or auction_end_time
     * @apiParam {String} sortOrder sort order. should be one of ASC, asc, DESC or desc
     *
     * @apiSuccess {Json} matching auction items as data and total record count as meta
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *  "data": [
     *    {
     *      "id": 1,
     *      "name": "Et enim cum quia ut.",
     *      "description": "Quis sed libero assumenda reiciendis distinctio maxime. Aut commodi ut error qui et ipsum. Facere modi tempore sint quisquam quisquam. Odio iure temporibus magni qui.",
     *      "price": 7339,
     *      "auction_end_time": "2021-04-15 19:04:36",
     *      "owner_id": null,
     *      "created_at": "2021-04-15T17:42:25.000000Z",
     *      "updated_at": "2021-04-15T17:42:25.000000Z"
     *    },
     *    {
     *      "id": 2,
     *      "name": "Delectus maiores officiis culpa omnis provident.",
     *      "description": "Beatae incidunt quia nam. Laboriosam quia autem qui. Aperiam et molestias tempore non molestiae.",
     *      "price": 6551,
     *      "auction_end_time": "2021-04-21 01:04:26",
     *      "owner_id": null,
     *      "created_at": "2021-04-15T17:42:25.000000Z",
     *      "updated_at": "2021-04-15T17:42:25.000000Z"
     *    },
     *    {
     *      "id": 3,
     *      "name": "Eaque error qui omnis dolorem sed et voluptatem.",
     *      "description": "Dicta ad alias cupiditate tenetur illum. Consequatur non quos ut enim. Itaque dolor ad harum doloremque earum aut accusantium.",
     *      "price": 1429,
     *      "auction_end_time": "2021-04-25 09:04:21",
     *      "owner_id": null,
     *      "created_at": "2021-04-15T17:42:25.000000Z",
     *      "updated_at": "2021-04-15T17:42:25.000000Z"
     *    },
     *  ],
     *  "meta": {
     *    "total": 100
     *   }
     *  }
     *
     * @apiError ValidationError validation error
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "error": "limit field should be an integer"
     *     }
     */
    public function index() {
        $filter = request('filter', '');
        $offset = request('offset', '');
        $limit = request('limit', '');
        $sortField = request('sortField', '');
        $sortOrder = request('sortOrder', '');
        $validator = Validator::make(
            [
                'filter'=>$filter,
                'offset'=>$offset,
                'limit'=>$limit,
                'sortField'=>$sortField,
                'sortOrder'=>$sortOrder
            ],
            [
                'filter'=>'string',
                'offset'=>'integer|min:0',
                'limit'=>'integer|min:0',
                'sortField'=>'string|in:name,price,auction_end_time',
                'sortOrder'=>'string|in:asc,ASC,desc,DESC'
        ]);
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors()->toJson());
        }
        $query = Item::where('name', 'like', '%'.$filter.'%')
            ->orWhere('description', 'like', '%'.$filter.'%');
        if ($sortField && $sortOrder) {
            $query->orderBy($sortField, $sortOrder);
        }
        $total = $query->count();
        if ($offset !== '') {
            $query->offset($offset);
        }
        if ($limit !== '') {
            $query->limit($limit);
        }
        return response()->json(['data'=>$query->get(), 'meta'=>['total'=>$total]], 200);
    }

    /**
     * @api {get} api/item/:id Get an auction item
     * @apiName GetAuctionItem
     * @apiGroup Auction
     *
     * @apiParam {Number} id Id of the action item
     *
     * @apiSuccess {Json} matching auction item as data
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *  "data":
     *    {
     *      "id": 1,
     *      "name": "Et enim cum quia ut.",
     *      "description": "Quis sed libero assumenda reiciendis distinctio maxime. Aut commodi ut error qui et ipsum. Facere modi tempore sint quisquam quisquam. Odio iure temporibus magni qui.",
     *      "price": 7339,
     *      "auction_end_time": "2021-04-15 19:04:36",
     *      "owner_id": null,
     *      "created_at": "2021-04-15T17:42:25.000000Z",
     *      "updated_at": "2021-04-15T17:42:25.000000Z"
     *    }
     *  }
     *
     * @apiError ValidationError validation error
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "error": "id field should be an integer"
     *     }
     */
    public function show($id) {
        $validator = Validator::make(['id'=>$id],[
            'id' => 'required|integer|min:1'
        ]);
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors()->toJson());
        }
        return response()->json(['data'=>Item::find($id)], 200);
    }
}
