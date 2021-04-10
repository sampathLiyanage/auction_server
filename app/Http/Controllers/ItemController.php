<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;

class ItemController extends Controller
{
    public function index() {
        $filter = request('filter', '');
        $offset = request('offset', false);
        $limit = request('limit', false);
        $sortField = request('sortField', '');
        $sortOrder = request('sortOrder', '');
        $query = Item::where('name', 'like', '%'.$filter.'%')
            ->orWhere('description', 'like', '%'.$filter.'%');
        if ($sortField && $sortOrder) {
            $query->orderBy($sortField, $sortOrder);
        }
        if ($offset) {
            $query->offset($offset);
        }
        if ($limit) {
            $query->limit($limit);
        }
        return $query->get();
    }

    public function show($id) {
        return Item::find($id);
    }
}
