<?php

namespace Tests\Feature;

use App\Bid;
use App\Item;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class BidControllerTest extends TestCase
{
    public function testSearchWithoutItemId() {
        $user = factory(User::class)->create();
        $item = factory(Item::class)->create();
        factory(Bid::class)->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $this->json('GET', 'api/bids')
            ->assertStatus(400)
            ->assertJson(["error" => '{"itemId":["The item id field is required."]}']);
    }

    public function testSearchWithValidItemId() {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $item = factory(Item::class)->create();
        factory(Bid::class)->create([
            'user_id' => $user2->id,
            'item_id' => $item->id,
            'is_auto_bid' => 1,
            'amount' => '3235.12'
        ]);
        factory(Bid::class)->create([
            'user_id' => $user1->id,
            'item_id' => $item->id,
            'amount' => '124.23'
        ]);
        $response = $this->json('GET', 'api/bids?itemId='.$item->id)
            ->assertStatus(200);
        $responseData = json_decode($response->getContent(),true)['data'];
        $this->assertCount(2, $responseData);
        $this->assertEquals($user1->id, $responseData[0]['user_id']);
        $this->assertEquals($user2->id, $responseData[1]['user_id']);
        $this->assertEquals($item->id, $responseData[0]['item_id']);
        $this->assertEquals($item->id, $responseData[1]['item_id']);
        $this->assertEquals('0', $responseData[0]['is_auto_bid']);
        $this->assertEquals('1', $responseData[1]['is_auto_bid']);
        $this->assertEquals('124.23', $responseData[0]['amount']);
        $this->assertEquals('3235.12', $responseData[1]['amount']);

        $this->json('GET', 'api/bids?itemId=1000')
            ->assertStatus(200)
            ->assertJson([]);
    }

    public function testSearchWithInvalidItemId() {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $item = factory(Item::class)->create();
        factory(Bid::class)->create([
            'user_id' => $user1->id,
            'item_id' => $item->id,
            'amount' => '124.23'
        ]);
        factory(Bid::class)->create([
            'user_id' => $user2->id,
            'item_id' => $item->id,
            'is_auto_bid' => 1,
            'amount' => '3235.12'
        ]);
        $this->json('GET', 'api/bids?itemId=sdfd')
            ->assertStatus(400)
            ->assertJson(['error'=>'{"itemId":["The item id must be an integer."]}']);
        $this->json('GET', 'api/bids?itemId=-1')
            ->assertStatus(400)
            ->assertJson(['error'=>'{"itemId":["The item id must be at least 1."]}']);
        $this->json('GET', 'api/bids?itemId=0')
            ->assertStatus(400)
            ->assertJson(['error'=>'{"itemId":["The item id must be at least 1."]}']);
    }

    public function testStoreWithValidInput() {
        list($user1, $user2, $item, $token) = $this->populateDataForTestStore();
        $response = $this->json('POST', 'api/bids',
            ['amount'=>'4256', 'user_id'=>$user1->id, 'item_id'=>$item->id],
            ['Authorization'=>'Bearer '.$token])
            ->assertStatus(201);
        $responseData = json_decode($response->getContent(),true)['data'];
        $this->assertEquals($user1->id, $responseData['user_id']);
        $this->assertEquals($item->id, $responseData['item_id']);
        $this->assertTrue(!isset($responseData['is_auto_bid']));
        $this->assertEquals('4256', $responseData['amount']);
    }

    public function testStoreWithUserOtherThanLoggedInUser() {
        list($user1, $user2, $item, $token) = $this->populateDataForTestStore();
        $this->json('POST', 'api/bids',
            ['amount'=>'4256', 'user_id'=>$user2->id, 'item_id'=>$item->id],
            ['Authorization'=>'Bearer '.$token])
            ->assertStatus(401)
        ->assertJson(['error'=>'Unauthorized']);
    }

    /**
     * @dataProvider storeWithInvalidParametersDataProvider
     * @param $amount
     * @param $userId
     * @param $itemId
     * @param $isAutoBidding
     * @param $token
     * @param $statusCode
     */
    public function testStoreWithInvalidParameters($amount, $userId, $itemId, $isAutoBidding, $statusCode) {
        list($user1, $user2, $item, $token) = $this->populateDataForTestStore();
        $userId = ($userId === false)?$user1->id:$userId;
        $itemId = ($itemId === false)?$item->id:$itemId;
        $this->json('POST', 'api/bids',
            ['amount'=>$amount, 'user_id'=>$userId, 'item_id'=>$itemId],
            ['Authorization'=>'Bearer '.$token])
            ->assertStatus($statusCode);
    }

    public function storeWithInvalidParametersDataProvider() {
        return [
            [-1, false, false, 0, 400],
            [0, false, false, 0, 400],
            [4123, false, false, 0, 201],
            ['asdf', false, false, 0, 400],
            [4123, -1, false, 0, 400],
            [4123, 0, false, 0, 400],
            [4123, 'asdfd', false, 0, 400],
            [4123, false, -1, 0, 400],
            [4123, false, 0, 0, 400],
            [4123, false, 'asdfd', 0, 400],
            [4123, false, false, 1, 201],
            [123, false, false, 1, 400]
        ];
    }

    protected function populateDataForTestStore() {
        $token = Str::random(60);
        $user1 = factory(User::class)->create([
            'api_token'=>$token
        ]);
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $item = factory(Item::class)->create();
        factory(Bid::class)->create([
            'user_id' => $user1->id,
            'item_id' => $item->id,
            'amount' => '124.23'
        ]);
        factory(Bid::class)->create([
            'user_id' => $user3->id,
            'item_id' => $item->id,
            'is_auto_bid' => 1,
            'amount' => '3235.12'
        ]);
        return [$user1, $user2, $item, $token];
    }
}
