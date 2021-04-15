<?php
namespace App\Lib;

use App\Bid;
use Illuminate\Support\Facades\DB;

class AutoBidDefaultStrategy implements AutoBidStrategy
{
    public function handleAutoBid($itemId = null, $userId = null) {
        $itemIdsWithAutoBidding = $this->getItemIdsWithAutoBidding($itemId, $userId);
        foreach ($itemIdsWithAutoBidding as $itemId) {
            $this->autoBid($itemId, $userId);
        }
    }

    protected function getItemIdsWithAutoBidding($itemId = null, $userId = null) {
        $query = DB::table('auto_bid_statuses')
            ->leftJoin('items', 'auto_bid_statuses.item_id', '=', 'items.id')
            ->select(['items.id', 'items.auction_end_time'])
            ->distinct('items.id');
        $where = [['auto_bid_statuses.auto_bid_enabled', '=', 1]];
        if (!is_null($itemId)) {
            $where[] = ['items.id', '=', $itemId];
        }
        if (!is_null($userId)) {
            $where[] = ['auto_bid_statuses.user_id', '=', $userId];
        }
        $itemsWithAutoBidding = $query->where($where)
            ->orderBy('items.auction_end_time')
            ->get(['items.id'])->toArray();

        return array_map(function ($item) {
            return $item->id;
        }, $itemsWithAutoBidding);
    }

    protected function autoBid($itemId, $userId = null) {
        $usersWithMaxBidConfigured = $this->getUsersWithMaxBidAmountConfigured($itemId, $userId);
        $currentMaxBidForItem = $this->getCurrentMaxBidOfItem($itemId);
        $currentMaxBidAmount = 0;
        if (!is_null($currentMaxBidForItem)) {
            $currentMaxBidAmount = $currentMaxBidForItem->amount;
        }
        $remainingAutoBidSums = [];
        $lastBid = $this->getLastBid($itemId);
        foreach ($usersWithMaxBidConfigured as $userWithMaxBidConfigured) {
            $currentBidSum = $this->getCurrentAutoBidSum($userWithMaxBidConfigured->user_id);
            if ($lastBid && $lastBid->user_id == $userWithMaxBidConfigured->user_id) {
                $currentBidSum -= $lastBid->amount;
            }
            $remainingBidAmount = $userWithMaxBidConfigured->max_bid_amount - $currentBidSum;
            if ($remainingBidAmount > $currentMaxBidAmount) {
                $remainingAutoBidSums[$userWithMaxBidConfigured->user_id] = $remainingBidAmount;
            }
        }
        $this->placeAutoBids($itemId, $remainingAutoBidSums, $currentMaxBidAmount);
    }

    protected function placeAutoBids($itemId, $remainingAutoBidSums, $currentMaxBidAmount) {
        if (count($remainingAutoBidSums) === 1) {
            $this->createNewBid(['amount'=> $currentMaxBidAmount+1, 'user_id'=>array_key_first($remainingAutoBidSums), 'item_id'=>$itemId, 'is_auto_bid'=>1]);
        } else if (count($remainingAutoBidSums) > 1) {
            asort($remainingAutoBidSums);
            $nextBidAmount = $currentMaxBidAmount+1;
            foreach ($remainingAutoBidSums as $userId => $remainingAutoBidSum) {
                $this->createNewBid(['amount'=> $nextBidAmount, 'user_id'=>$userId, 'item_id'=>$itemId, 'is_auto_bid'=>1]);
                $nextBidAmount = $remainingAutoBidSum + 1;
            }
        }
    }

    protected function createNewBid($params) {
        $lastBid = $this->getLastBid($params['item_id']);
        if (!$lastBid || $lastBid->user_id != $params['user_id']) {
            Bid::create($params);
        }
    }

    protected function getLastBid($itemId) {
        return Bid::where('item_id', '=', $itemId)
            ->orderBy('id', 'DESC')
            ->first();
    }

    protected function getCurrentAutoBidSum($userId) {
        $amounts = DB::table('bids')
            ->selectRaw('max(amount) as max_amount')
            ->where([['user_id', '=', $userId], ['is_auto_bid', '=', '1']])
            ->whereIn('id', function ($query) {
                $query->selectRaw('max(id)')
                    ->from('bids')
                    ->groupBy('item_id');
            })
            ->groupBy('item_id')->get()->toArray();
        return array_reduce($amounts, function($currentSum, $item) {
            return $currentSum + $item->max_amount;
        }, 0);
    }

    protected function getCurrentMaxBidOfItem($itemId) {
        return Bid::where('item_id', '=', $itemId)
            ->orderBy('amount', 'DESC')
            ->first();
    }

    protected function getUsersWithMaxBidAmountConfigured($itemId, $userId = null) {
        $query = DB::table('auto_bid_statuses')
            ->leftJoin('configurations', 'auto_bid_statuses.user_id', '=', 'configurations.user_id')
            ->select(['configurations.user_id as user_id', 'configurations.max_bid_amount as max_bid_amount'])
            ->whereNotNull(['configurations.max_bid_amount']);

        $where = [['auto_bid_statuses.item_id', '=', $itemId], ['auto_bid_statuses.auto_bid_enabled', '=', 1]];
        if (!is_null($userId)) {
            $where[] = ['auto_bid_statuses.user_id', '=', $userId];
        }
        return $query->where($where)
            ->orderBy('configurations.max_bid_amount')
            ->get();
    }
}
