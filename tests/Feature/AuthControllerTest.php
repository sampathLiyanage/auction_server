<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function testLoginWithoutCredentials() {
        $this->json('POST', 'api/login')
            ->assertStatus(400)
            ->assertJson([
                'error' => '{"username":["The username field is required."],"password":["The password field is required."]}'
            ]);
    }

    public function testLoginWithInvalidCredentials() {
        $this->json('POST', 'api/login', ["username"=>"user1", "password"=>"user1"])
            ->assertStatus(401)
            ->assertJson([
                'error' => 'Login Credentials Mismatch'
            ]);
    }

    public function testLoginWithValidCredentials() {
        factory(User::class)->create([
            'name' => 'user1',
            'password' => md5('user1'),
        ]);
        $response = $this->json('POST', 'api/login', ["username"=>"user1", "password"=>"user1"])
            ->assertStatus(200)
            ->assertJsonPath("data.name","user1");
        $responseArray = json_decode($response->getContent(), true);
        $this->assertEquals(60, strlen($responseArray['data']['api_token']));
    }

    public function testLogoutWithoutToken() {
        $this->json('POST', 'api/logout')
            ->assertStatus(400)
            ->assertJson([
                'error' => '{"token":["The token field is required."]}'
            ]);
    }

    public function testLogoutWithToken() {
        factory(User::class)->create([
            'name' => 'user1',
            'password' => md5('user1'),
            'api_token' => 'YHkEM0R3uRBcE5ieVAC0MCmXUDfUpbnwkHH4Tc3X1wZtXI8KMqCuO3yno7Rp'
        ]);
        $this->json('POST', 'api/logout', ["token"=>"invalidToken"])
            ->assertStatus(400)
            ->assertJsonPath("error","Invalid Token");
        $this->json('POST', 'api/logout', ["token"=>"YHkEM0R3uRBcE5ieVAC0MCmXUDfUpbnwkHH4Tc3X1wZtXI8KMqCuO3yno7Rp"])
            ->assertStatus(200)
            ->assertJsonPath("message","Logged Out Successfully");
    }
}
