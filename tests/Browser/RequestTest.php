<?php

namespace Tests\Browser;

use App\Models\Request;
use Carbon\Carbon;
use Facebook\WebDriver\WebDriverKeys;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Keyboard;
use Tests\DuskTestCase;
use Tests\XPathHelpers;

class RequestTest extends DuskTestCase
{
    use XPathHelpers;

    private static function getRequestData(): array
    {
        return [
            "anon" => [
                "title" => "Gdybyś była ze mną",
                "artist" => "The Brokers",
                "link" => "https://www.youtube.com/watch?v=dQw4w9WgXcQ",
                "wishes" => "Poproszę spokojniej niż oryginał",
                "price_code" => "c",
                "price" => 80,
                "deadline" => Carbon::today()->addDays(2),
            ],
        ];
    }

    public function test_happy_path_request(): void
    {
        $this->browse(function(Browser $client, Browser $archmage) {
            $rd = self::getRequestData()["anon"];

            $this->openPodkladyModal($client);
            $client->type("client_name", "Tomasz Torpeda")
                ->type("email", "ttorpeda@torpeda-industries.bong")
                ->type("phone", "123778924")
                ->type("title", $rd["title"])
                ->type("artist", $rd["artist"])
                ->type("link", $rd["link"])
                ->type("wishes", $rd["wishes"])
                ->type("test", "20")
                ->waitForReload(function (Browser $browser) {
                    $browser->clickAtXPath(self::x("class", "button", "Zatwierdź"));
                })
                ->assertSee("Zapytanie zostało pomyślnie dodane");

            $this->openArchmageDashboard($archmage);
            $archmage->assertSeeIn('.section[data-title="Zapytania"]', $rd["title"])
                ->assertSee("nowe")
                ->waitForReload(function (Browser $browser) use ($rd) {
                    $browser->click('[role="model-card"][data-model="'.$rd["title"].' dla: Tomasz Torpeda"] .button[data-tippy="Szczegóły"]');
                })
                ->assertSee(implode(" – ", [$rd["artist"], $rd["title"]]));
            $this->select($archmage, "#genre_id", "rock");
            $archmage->clear("wishes")
                ->type("price_code", $rd["price_code"])
                ->type("deadline", $rd["deadline"]->format("d.m.Y"))
                ->waitFor("#price-summary table")
                ->assertSeeIn("#price-summary", $rd["price"])
                ->assertValueIsNot("#deadline", "");
            $archmage->waitForReload(function (Browser $browser) {
                $browser->click('.button[data-tippy="Oddaj"]');
            })
                ->assertSee("wycena do akceptacji");

            $request = Request::firstWhere([
                ["title", $rd["title"]],
                ["client_name", "Tomasz Torpeda"],
            ]);

            $client->visitRoute("request", ["id" => $request->id])
                ->assertSee("wycena do akceptacji")
                ->assertSee($rd["title"])
                ->assertSee("Poproś o zmiany do utworu")
                ->assertSeeIn(".card[data-title='Płatność']", $rd["price"])
                ->assertSee("przelew na konto")
                ->assertSee("Termin realizacji")
                ->assertSeeIn(".card[data-title='Termin realizacji']", $rd["deadline"]->format("Y-m-d"))
                ->assertSee("Poproś o szybszą realizację")
                ->assertSee("Pliki będą dostępne");
            $client->clickAtXPath(self::x("class", "button", "Kliknij tutaj, aby potwierdzić warunki zlecenia"))
                ->waitFor("#modal-card")
                ->with("#modal-card", fn ($modal) => $modal
                    ->assertSee("Zaznacz poniższe zgody")
                    ->assertSee("Tytuł, linki i życzenia do utworu są poprawne")
                    ->assertSee("Zapłacę kwotę w wysokości $rd[price]")
                    ->assertSee("dostęp do plików otrzymam ".$rd["deadline"]->format("d.m.Y"))
                )
                ->check("confirm_song")
                ->check("confirm_price")
                ->check("confirm_deadline");
            $client->waitForReload(function (Browser $browser) {
                $browser->clickAtXPath(self::x("class", "button", "Zatwierdź"));
            })
                ->assertSee("przyjęte")
                ->assertSee("Utworzyłem dla Ciebie konto")
                ->assertSee("następujące hasło")
                ->assertSee("Zaloguj się");
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

    private function openClientDashboard(Browser $browser, int $user_id)
    {
        //
    }

    private function select(Browser $browser, string $input_selector, mixed $value)
    {
        $browser->click(".choices:has($input_selector)")
            ->withKeyboard(fn (Keyboard $kbd) => $kbd->type($value)->press(WebDriverKeys::ENTER));
    }
    #endregion
}
