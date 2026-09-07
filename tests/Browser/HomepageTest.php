<?php

namespace Tests\Browser;

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
}
