<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Item;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
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
