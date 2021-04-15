<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginWithoutCredentials() {
        $this->json('POST', 'api/login')
            ->assertStatus(400)
            ->assertJson([
                'error' => '{"username":["The username field is required."],"password":["The password field is required."]}'
            ]);
    }

    public function testLoginWithInvalidCredentials() {
        $this->json('POST', 'api/login', ["username"=>"usery", "password"=>"usery"])
            ->assertStatus(401)
            ->assertJson([
                'error' => 'Login Credentials Mismatch'
            ]);
    }

    public function testLoginWithValidCredentials() {
        factory(User::class)->create([
            'name' => 'userx',
            'password' => md5('userx'),
        ]);
        $response = $this->json('POST', 'api/login', ["username"=>"userx", "password"=>"userx"])
            ->assertStatus(200)
            ->assertJsonPath("data.name","userx");
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
            'name' => 'userx',
            'password' => md5('userx'),
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
