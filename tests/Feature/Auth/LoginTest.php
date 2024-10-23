<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
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
}
