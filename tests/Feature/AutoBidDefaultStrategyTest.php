<?php

namespace Tests\Feature;

use App\AutoBidStatus;
use App\Bid;
use App\Configuration;
use App\Lib\AutoBidDefaultStrategy;
use App\User;
use App\Item;
use Tests\TestCase;

class AutoBidDefaultStrategyTest extends TestCase
{
    private $autoBidDefaultStrategy;

    public function setUp(): void {
        $this->autoBidDefaultStrategy = new AutoBidDefaultStrategy();
        parent::setUp();
    }

    public function testSingleUserAutoBid() {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $item1 = factory(Item::class)->create();
        $configuration1 = factory(Configuration::class)->create([
            'user_id' => $user1->id,
            'max_bid_amount' => 4000
        ]);
        $autoBidStatus1 = factory(AutoBidStatus::class)->create([
            'user_id' => $user1->id,
            'item_id' => $item1->id,
            'auto_bid_enabled' => true
        ]);
        factory(Bid::class)->create([
            'amount' => 1014,
            'user_id' => $user2->id,
            'item_id' => $item1->id
        ]);
        $this->autoBidDefaultStrategy->handleAutoBid($item1->id);

        $this->assertDatabaseHas('bids', [
            'amount' => 1015,
            'user_id' => $user1->id,
            'item_id' => $item1->id
        ]);
        factory(Bid::class)->create([
            'amount' => 1019,
            'user_id' => $user2->id,
            'item_id' => $item1->id
        ]);
        $this->autoBidDefaultStrategy->handleAutoBid($item1->id);
        $this->assertDatabaseHas('bids', [
            'amount' => 1020,
            'user_id' => $user1->id,
            'item_id' => $item1->id
        ]);
        factory(Bid::class)->create([
            'amount' => 4000,
            'user_id' => $user2->id,
            'item_id' => $item1->id
        ]);
        $this->autoBidDefaultStrategy->handleAutoBid($item1->id);
        $this->assertDatabaseMissing('bids', [
            'amount' => 4001,
            'user_id' => $user1->id,
            'item_id' => $item1->id
        ]);
    }

    public function testTowUsersAutoBid() {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $item1 = factory(Item::class)->create();
        $configuration1 = factory(Configuration::class)->create([
            'user_id' => $user1->id,
            'max_bid_amount' => 3000
        ]);
        $configuration2 = factory(Configuration::class)->create([
            'user_id' => $user2->id,
            'max_bid_amount' => 4000
        ]);
        $autoBidStatus1 = factory(AutoBidStatus::class)->create([
            'user_id' => $user1->id,
            'item_id' => $item1->id,
            'auto_bid_enabled' => true
        ]);
        $autoBidStatus2 = factory(AutoBidStatus::class)->create([
            'user_id' => $user2->id,
            'item_id' => $item1->id,
            'auto_bid_enabled' => true
        ]);
        factory(Bid::class)->create([
            'amount' => 1014,
            'user_id' => $user2->id,
            'item_id' => $item1->id
        ]);
        $this->autoBidDefaultStrategy->handleAutoBid($item1->id);
        $this->assertDatabaseHas('bids', [
            'amount' => 1015,
            'user_id' => $user1->id,
            'item_id' => $item1->id
        ]);
        $this->assertDatabaseHas('bids', [
            'amount' => 3001,
            'user_id' => $user2->id,
            'item_id' => $item1->id
        ]);
        factory(Bid::class)->create([
            'amount' => 3002,
            'user_id' => $user3->id,
            'item_id' => $item1->id
        ]);
        $this->autoBidDefaultStrategy->handleAutoBid($item1->id);
        $this->assertDatabaseHas('bids', [
            'amount' => 3003,
            'user_id' => $user2->id,
            'item_id' => $item1->id
        ]);
        factory(Bid::class)->create([
            'amount' => 4000,
            'user_id' => $user3->id,
            'item_id' => $item1->id
        ]);
        $this->assertDatabaseMissing('bids', [
            'amount' => 4001,
            'user_id' => $user2->id,
            'item_id' => $item1->id
        ]);
    }

    public function testTowUsersAutoBidWithTwoItems() {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $item1 = factory(Item::class)->create();
        $item2 = factory(Item::class)->create();
        $configuration1 = factory(Configuration::class)->create([
            'user_id' => $user1->id,
            'max_bid_amount' => 3000
        ]);
        $configuration2 = factory(Configuration::class)->create([
            'user_id' => $user2->id,
            'max_bid_amount' => 4000
        ]);
        $autoBidStatus1 = factory(AutoBidStatus::class)->create([
            'user_id' => $user1->id,
            'item_id' => $item1->id,
            'auto_bid_enabled' => true
        ]);
        $autoBidStatus2 = factory(AutoBidStatus::class)->create([
            'user_id' => $user2->id,
            'item_id' => $item1->id,
            'auto_bid_enabled' => true
        ]);
        $autoBidStatus3 = factory(AutoBidStatus::class)->create([
            'user_id' => $user1->id,
            'item_id' => $item2->id,
            'auto_bid_enabled' => true
        ]);
        $autoBidStatus4 = factory(AutoBidStatus::class)->create([
            'user_id' => $user2->id,
            'item_id' => $item2->id,
            'auto_bid_enabled' => true
        ]);
        factory(Bid::class)->create([
            'amount' => 1014,
            'user_id' => $user2->id,
            'item_id' => $item1->id
        ]);
        $this->autoBidDefaultStrategy->handleAutoBid($item1->id);
        $this->assertDatabaseHas('bids', [
            'amount' => 1015,
            'user_id' => $user1->id,
            'item_id' => $item1->id
        ]);
        $this->assertDatabaseHas('bids', [
            'amount' => 3001,
            'user_id' => $user2->id,
            'item_id' => $item1->id
        ]);
        $this->autoBidDefaultStrategy->handleAutoBid($item2->id);
        $this->assertDatabaseHas('bids', [
            'amount' => 1,
            'user_id' => $user2->id,
            'item_id' => $item2->id
        ]);
        $this->assertDatabaseHas('bids', [
            'amount' => 1000,
            'user_id' => $user1->id,
            'item_id' => $item2->id
        ]);
    }
}
