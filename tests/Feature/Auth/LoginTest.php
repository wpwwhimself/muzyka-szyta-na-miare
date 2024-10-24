<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    public function testUserCanViewALoginForm()
    {
        $res = $this->get(route("login"));
        $res->assertSuccessful();
        $res->assertViewIs("auth.login");
    }

    public function testUserCannotViewALoginFormWhenAuthenticated()
    {
        $user = User::factory()->make();
        $res = $this->actingAs($user)->get(route("login"));
        $res->assertRedirect(route("dashboard"));
    }

    public function testUserCanLoginWithCorrectCredentials()
    {
        $password = Str::random(10);

        $user = User::factory()->create([
            "password" => $password,
        ]);
        $res = $this->post(route("authenticate"), [
            "password" => $password,
        ]);
        $res->assertRedirect(route("dashboard"));
        $this->assertAuthenticatedAs($user);
    }

    public function testUserCannotLoginWithIncorrectCredentials()
    {
        $password = Str::random(10);

        $user = User::factory()->create([
            "password" => $password,
        ]);
        $password .= $password;
        $res = $this->from(route("login"))->post(route("authenticate"), [
            "password" => $password,
        ]);
        $res->assertRedirect(route("login"));
        $res->assertSessionHas("error");
        $this->assertGuest();
    }

    public function testUserCanLogout()
    {
        $user = User::factory()->make();
        $res = $this->actingAs($user)->get(route("logout"));
        $res->assertRedirect(route("home-podklady"));
        $this->assertGuest();
    }
}
