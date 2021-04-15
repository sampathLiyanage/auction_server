<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutoBidStatus extends Model
{
    protected $fillable = ['user_id', 'item_id', 'auto_bid_enabled'];
}
