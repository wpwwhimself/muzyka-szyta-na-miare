<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Keyboard;
use Tests\DuskTestCase;
use Tests\XPathHelpers;

class RequestTest extends DuskTestCase
{
    use XPathHelpers;

    public function test_mailable_anon_can_create_request(): void
    {
        $this->browse(function(Browser $browser) {
            $this->openPodkladyModal($browser);

            $browser->type("client_name", "Tomasz Torpeda")
                ->type("email", "ttorpeda@torpeda-industries.bong")
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

    public function test_archmage_can_process_mailable_anons_request(): void
    {
        $this->browse(function(Browser $browser) {
            $this->openArchmageDashboard($browser);

            $browser->assertSeeIn('.section[data-title="Zapytania"]', "Gdybyś była ze mną")
                ->waitForReload(function (Browser $browser) {
                    $browser->click('[role="model-card"][data-model="Gdybyś była ze mną dla: Tomasz Torpeda"] .button[data-tippy="Szczegóły"]');
                })
                ->assertSee("The Brokers – Gdybyś była ze mną");

            $this->select($browser, "#genre_id", "rock");
            $browser->clear("wishes")
                ->type("price_code", "c")
                ->scrollIntoView(".calendar-table")
                ->click(".calendar-table .suggest")
                ->waitFor("#price-summary table")
                ->assertSee("80,00 zł")
                ->assertValueIsNot("#deadline", "");

            $browser->waitForReload(function (Browser $browser) {
                $browser->click('.button[data-tippy="Oddaj"]');
            })
                ->assertSee("Zapytanie gotowe, mail wysłany");
        });
    }

    #region helpers
    private function openPodkladyModal(Browser $browser)
    {
        $browser->visit("/")
            ->waitForText("Podkłady i nuty")
            ->clickAtXPath(self::x("role", "service-button", "Podkłady i nuty"))
            ->waitForText("Złóż zapytanie")
            ->clickAtXPath(self::x("class", "button", "Złóż zapytanie"))
            ->waitFor("#modal-card");
    }

    private function openArchmageDashboard(Browser $browser)
    {
        $browser->loginAs(1)
            ->visit("/profile");
    }

    private function select(Browser $browser, string $input_selector, mixed $value)
    {
        $browser->click(".choices:has($input_selector)")
            ->keys(".choices:has($input_selector) .choices__list .choices__input", [$value, "{enter}"]);
    }
    #endregion
}
