<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\XPathHelpers;

class HomepageTest extends DuskTestCase
{
    use XPathHelpers;

    public function test_homepage_is_loading(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Muzyka Szyta Na Miarę')
                ->assertTitleContains("Muzyka Szyta Na Miarę");
        });
    }

    public function test_subject_changes_are_working(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit("/")
                ->assertSee("Podkłady i nuty")
                ->assertSee("Oprawa ślubów")
                ->assertSee("Imprezy i koncerty");

            $browser->clickAtXPath(self::x("role", "service-button", "Podkłady"))
                ->waitForText("Co mogę dla Ciebie zrobić?")
                ->assertSee("Złóż zapytanie");

            $browser->clickAtXPath(self::x("role", "service-button", "Oprawa ślubów"))
                ->waitForText("Jak mogę wzbogacić Twoją uroczystość?")
                ->assertSee("Złóż zapytanie");

            $browser->clickAtXPath(self::x("role", "service-button", "Imprezy i koncerty"))
                ->waitForText("Jak mogę uświetnić Twoją imprezę?")
                ->assertSee("Złóż zapytanie");
        });
    }

    public function test_user_can_send_podklady_request(): void
    {
        $this->browse(function(Browser $browser) {
            $browser->visit("/")
                ->waitForText("Podkłady i nuty")
                ->clickAtXPath(self::x("role", "service-button", "Podkłady i nuty"))
                ->waitForText("Złóż zapytanie")
                ->clickAtXPath(self::x("class", "button", "Złóż zapytanie"))
                ->waitFor("#modal-card")
                ->assertSee("Wyślij zapytanie")
                ->assertSee("Imię i nazwisko")
                ->assertSee("Tytuł utworu")
                ->assertSee("Zatwierdź");

            $browser->type("client_name", "Tomasz Torpeda")
                ->type("email", "ttorpeda@torpeda-industries.test")
                ->type("phone", "123778924")
                ->type("title", "Gdybyś była ze mną")
                ->type("artist", "The Brokers")
                ->type("link", "https://www.youtube.com/watch?v=dQw4w9WgXcQ")
                ->type("wishes", "Proszę spokojniej niż oryginał")
                ->type("test", "20")
                ->waitForReload(function (Browser $browser) {
                    $browser->clickAtXPath(self::x("class", "button", "Zatwierdź"));
                })
                ->assertSee("Zapytanie zostało pomyślnie dodane");
        });
    }
}
