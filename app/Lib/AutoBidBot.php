<?php
namespace App\Lib;

class AutoBidBot
{
    private $strategy;

    public function __construct(AutoBidStrategy $strategy) {
        $this->strategy = $strategy;
    }

    public function autoBid($itemId = null, $userId = null) {
        $this->strategy->handleAutoBid($itemId, $userId);
    }
}
