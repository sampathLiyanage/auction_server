<?php
namespace App\Lib;

interface AutoBidStrategy
{
    public function handleAutoBid($itemId, $userId);
}
