<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $fillable = ['amount', 'user_id', 'item_id', 'is_auto_bid'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
