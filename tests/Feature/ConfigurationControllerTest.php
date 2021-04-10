<?php

namespace Tests\Feature;

use App\Configuration;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class ConfigurationControllerTest extends TestCase
{
    public function testShowConfiguration() {
        list($user1, $user2, $token) = $this->populateConfiguration();
        $response = $this->json('GET', 'api/configurations/'.$user1->id, [], ['Authorization'=>'Bearer '.$token])
            ->assertStatus(200);
        $responseData = json_decode($response->getContent(),true);
        $this->assertEquals('{"max_bid_amount": "13923.35"}', $responseData['configuration']);
    }

    public function testShowConfigurationWithUserOtherThanLoggedInUser() {
        list($user1, $user2, $token) = $this->populateConfiguration();
        $this->json('GET', 'api/configurations/'.$user2->id, [], ['Authorization'=>'Bearer '.$token])
            ->assertStatus(401);
    }

    /**
     * @dataProvider showConfigurationWithInvalidUserIdDataProvider
     * @param $userId
     * @param $statusCode
     */
    public function testShowConfigurationWithInvalidUserId($userId, $statusCode) {
        list($user1, $user2, $token) = $this->populateConfiguration();
        $this->json('GET', 'api/configurations/'.$userId, [], ['Authorization'=>'Bearer '.$token])
            ->assertStatus($statusCode);
    }

    public function showConfigurationWithInvalidUserIdDataProvider() {
        return [
            [-1, 400],
            [0, 400],
            ['sdfdf', 400],
        ];
    }

    public function testSaveNewConfiguration() {
        $token = Str::random(60);
        $user = factory(User::class)->create([
            'api_token'=>$token
        ]);
        $response = $this->json('PATCH', 'api/configurations/'.$user->id, ['max_bid_amount'=>"23532.23"], ['Authorization'=>'Bearer '.$token])
            ->assertStatus(200);
        $responseData = json_decode($response->getContent(),true);
        $this->assertEquals('{"max_bid_amount":"23532.23"}', $responseData['configuration']);
    }

    public function testSaveExistingConfiguration() {
        list($user1, $user2, $token) = $this->populateConfiguration();
        $response = $this->json('PATCH', 'api/configurations/'.$user2->id, ['max_bid_amount'=>"23532.23"], ['Authorization'=>'Bearer '.$token])
            ->assertStatus(401);
    }

    /**
     * @dataProvider configurationFormatWhenSavingDataProvider
     * @param $config
     * @param $userId
     * @param $status
     */
    public function testConfigurationFormatWhenSaving($config, $userId, $status) {
        list($user1, $user2, $token) = $this->populateConfiguration();
        $userId = is_null($userId)?$user1->id:$userId;
        $this->json('PATCH', 'api/configurations/'.$userId, $config, ['Authorization'=>'Bearer '.$token])
            ->assertStatus($status);
    }

    public function configurationFormatWhenSavingDataProvider() {
        return [
            [['max_bid_amount'=>"-12.7"], null, 400],
            [['max_bid_amount'=>"0"], null, 200],
            [['max_bid_amount'=>"ssdf"], null, 400],
            [['invalid_key'=>"ssdf"], null, 400],
            [['max_bid_amount'=>"300"], "0", 400],
            [['max_bid_amount'=>"300"], "-1", 400],
            [['max_bid_amount'=>"300"], "sdfdd", 400],
        ];
    }

    protected function populateConfiguration() {
        $token = Str::random(60);
        $user1 = factory(User::class)->create([
            'api_token'=>$token
        ]);
        $user2 = factory(User::class)->create();
        factory(Configuration::class)->create([
            'configuration' => '{"max_bid_amount": "13923.35"}'
        ]);
        return [$user1, $user2, $token];
    }
}
