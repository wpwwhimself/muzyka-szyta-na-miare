<?php

namespace Tests\Browser;

use App\Models\Request;
use Carbon\Carbon;
use Facebook\WebDriver\WebDriverKeys;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Keyboard;
use Override;
use Tests\DuskTestCase;
use Tests\XPathHelpers;

class RequestTest extends DuskTestCase
{
    use XPathHelpers;

    #region setup
    public function tearDown(): void
    {
        session()->flush();
        parent::tearDown();
        $this->browse(function (Browser $browser) {
            $browser->driver->manage()->deleteAllCookies();
        });
    }

    private static function getRequestData(): array
    {
        return [
            "anon" => [
                "client_name" => "Tomasz Torpeda",
                "title" => "Gdybyś była ze mną",
                "artist" => "The Brokers",
                "link" => "https://www.youtube.com/watch?v=dQw4w9WgXcQ",
                "wishes" => "Poproszę spokojniej niż oryginał",
                "price_code" => "c",
                "price" => 80,
                "deadline" => Carbon::today()->addDays(2),
            ],
            "client" => [
                "title" => "Poker face",
                "artist" => "The Pokers",
                "link" => "https://www.youtube.com/watch?v=fGJX-K1YRG0",
                "price_code" => "cx",
                "price" => 104,
                "deadline" => Carbon::today()->addDays(2),
            ],
            "anon_picky" => [
                "client_name" => "Tomasz Wybredny",
                "title" => "Nie taki diabeł straszny",
                "artist" => "Los Diablos",
                "link" => "https://www.youtube.com/watch?v=DncbhIOJrb8",
                "wishes" => "tonacja c-moll",
                "price_code" => "c",
                "price" => 80,
                "deadline" => Carbon::today()->addDays(2),
            ],
            "anon_impatient" => [
                "client_name" => "Tomasz Szybki",
                "title" => "Śpiewam sobie",
                "artist" => "Jolanta Śpiewająca",
                "link" => "https://www.youtube.com/watch?v=FK0Cj7f6_ZI",
                "wishes" => "z linią melodyczną dla córki",
                "price_code" => "b",
                "price" => 60,
                "deadline" => Carbon::today()->addDays(5),
            ],
            "anon_unhappy" => [
                "client_name" => "Tomasz Zawiedziony",
                "title" => "Wiem więcej",
                "artist" => "Andrzej Grześ",
                "link" => "https://www.youtube.com/watch?v=FK0Cj7f6_ZI",
                "price_code" => "b",
                "price" => 60,
                "deadline" => Carbon::today()->addDays(5),
            ],
            "anon_mailless" => [
                "client_name" => "Tomasz Bezmailowy",
                "title" => "Typowa piosenka",
                "artist" => "The Typers",
                "link" => "https://www.youtube.com/watch?v=BxeMuy2s_hI",
                "price_code" => "cx",
                "price" => 104,
                "deadline" => Carbon::today()->addDays(2),
            ],
            "anon_with_delay" => [
                "client_name" => "Tomasz Opóźniony",
                "title" => "Nie mamy nic",
                "artist" => "The Nicniehavers",
                "link" => "https://www.youtube.com/watch?v=NrG5olOLNOk",
                "price_code" => "c",
                "price" => 80,
                "deadline" => Carbon::today()->addDays(2),
                "delayed_payment" => Carbon::today()->addMonth()->floorMonth(),
            ],
        ];
    }
    #endregion

    public function test_new_request_from_anon(): void
    {
        $this->browse(function(Browser $client, Browser $archmage) {
            $rd = self::getRequestData()["anon"];

            $this->openPodkladyModal($client, [
                "client_name" => $rd["client_name"],
                "email" => "ttorpeda@torpeda-industries.bong",
                "phone" => "123778924",
                "title" => $rd["title"],
                "artist" => $rd["artist"],
                "link" => $rd["link"],
                "wishes" => $rd["wishes"],
                "test" => "20",
            ]);
            $client->waitForReload(function (Browser $browser) {
                $browser->clickAtXPath(self::x("class", "button", "Zatwierdź"));
            })
                ->assertSee("Zapytanie zostało pomyślnie dodane");

            $this->openArchmageDashboard($archmage);
            $archmage->with('.section[data-title="Zapytania"]', fn ($section) =>
                $section->assertSee($rd["title"])
                    ->assertSee("nowe")
            )
                ->waitForReload(function (Browser $browser) use ($rd) {
                    $browser->click('[role="model-card"][data-model="'.$rd["title"].' dla: '.$rd["client_name"].'"] .button[data-tippy="Szczegóły"]');
                })
                ->assertSee(implode(" – ", [$rd["artist"], $rd["title"]]));
            $this->fillOutRequestForArchmage($archmage, [
                "genre_id" => "jazz",
                "wishes" => null,
                "price_code" => $rd["price_code"],
                "deadline" => $rd["deadline"]->format("d.m.Y"),
            ]);
            $archmage->waitFor("#price-summary table")
                ->assertSeeIn("#price-summary", $rd["price"])
                ->assertValueIsNot("#deadline", "");
            $archmage->waitForReload(function (Browser $browser) {
                $browser->click('.button[data-tippy="Oddaj"]');
            })
                ->assertSee("wycena do akceptacji");

            $request = Request::firstWhere([
                ["title", $rd["title"]],
                ["client_name", $rd["client_name"]],
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

    public function test_new_request_from_mailless_anon(): void
    {
        $this->browse(function(Browser $client, Browser $archmage) {
            $rd = self::getRequestData()["anon_mailless"];

            $this->openPodkladyModal($client, [
                "client_name" => $rd["client_name"],
                "phone" => "123778925",
                "title" => $rd["title"],
                "artist" => $rd["artist"],
                "link" => $rd["link"],
                "test" => "20",
                "contact_preference" => "sms",
            ]);
            $client->waitForReload(function (Browser $browser) {
                $browser->clickAtXPath(self::x("class", "button", "Zatwierdź"));
            })
                ->assertSee("Zapytanie zostało pomyślnie dodane");

            $this->openArchmageDashboard($archmage);
            $archmage->with('.section[data-title="Zapytania"]', fn ($section) =>
                $section->assertSee($rd["title"])
                    ->assertSee("nowe")
            )
                ->waitForReload(function (Browser $browser) use ($rd) {
                    $browser->click('[role="model-card"][data-model="'.$rd["title"].' dla: '.$rd["client_name"].'"] .button[data-tippy="Szczegóły"]');
                })
                ->assertSee(implode(" – ", [$rd["artist"], $rd["title"]]));
            $this->fillOutRequestForArchmage($archmage, [
                "genre_id" => "jazz",
                "wishes" => null,
                "price_code" => $rd["price_code"],
                "deadline" => $rd["deadline"]->format("d.m.Y"),
            ]);
            $archmage->waitFor("#price-summary table")
                ->assertSeeIn("#price-summary", $rd["price"])
                ->assertValueIsNot("#deadline", "");
            $archmage->waitForReload(function (Browser $browser) {
                $browser->click('.button[data-tippy="Oddaj"]');
            })
                ->assertSee("wycena do akceptacji");

            $request = Request::firstWhere([
                ["title", $rd["title"]],
                ["client_name", $rd["client_name"]],
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

    public function test_new_request_from_existing_client(): void
    {
        $this->browse(function(Browser $client, Browser $archmage) {
            $rd = self::getRequestData()["client"];
            $rd["client_name"] = self::getRequestData()["anon"]["client_name"];

            $this->openClientDashboard($client, 2);
            $client->clickAtXPath(self::x("class", "button", "Złóż zapytanie o podkład/nuty"))
                ->waitFor("#modal-card");
            $this->fillOutPodkladyModal($client, [
                "title" => $rd["title"],
                "artist" => $rd["artist"],
                "link" => $rd["link"],
                "test" => "20",
            ]);
            $client->waitForReload(function (Browser $browser) {
                $browser->clickAtXPath(self::x("class", "button", "Zatwierdź"));
            })
                ->assertSee("Zapytanie dodane")
                ->assertSeeIn(".section[data-title='Na tapecie']", $rd["title"]);

            $this->openArchmageDashboard($archmage);
            $archmage->with('.section[data-title="Zapytania"]', fn ($section) => $section->assertSee($rd["title"])
                ->assertSee("nowe")
                ->assertSee($rd["client_name"])
            )
                ->waitForReload(function (Browser $browser) use ($rd) {
                    $browser->click('[role="model-card"][data-model="'.$rd["title"].' dla: '.$rd["client_name"].'"] .button[data-tippy="Szczegóły"]');
                })
                ->assertSee(implode(" – ", [$rd["artist"], $rd["title"]]));
            $this->fillOutRequestForArchmage($archmage, [
                "genre_id" => "reggae",
                "wishes" => null,
                "price_code" => $rd["price_code"],
                "deadline" => $rd["deadline"]->format("d.m.Y"),
            ]);
            $archmage->waitFor("#price-summary table")
                ->assertSeeIn("#price-summary", $rd["price"])
                ->assertValueIsNot("#deadline", "");
            $archmage->waitForReload(function (Browser $browser) {
                $browser->click('.button[data-tippy="Oddaj"]');
            })
                ->assertSee("wycena do akceptacji");

            $request = Request::firstWhere([
                ["title", $rd["title"]],
                ["client_name", $rd["client_name"]],
            ]);

            $client->visitRoute("profile")
                ->with(".section[data-title='Na tapecie']", fn ($section) => $section
                    ->assertSee("wycena do akceptacji")
                    ->assertSee($rd["title"])
                )
                ->waitForReload(function (Browser $browser) use ($rd) {
                    $browser->click('[role="model-card"][data-model="'.$rd["title"].' dla: '.$rd["client_name"].'"] .button');
                });
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
                ->assertSee("przyjęte");
        });
    }

    public function test_anon_can_object_to_a_request(): void
    {
        $this->browse(function(Browser $client, Browser $archmage) {
            $rd = self::getRequestData()["anon_picky"];

            $this->openPodkladyModal($client, [
                "client_name" => $rd["client_name"],
                "email" => "iampicky@test.test",
                "phone" => "123778926",
                "title" => $rd["title"],
                "artist" => $rd["artist"],
                "link" => $rd["link"],
                "wishes" => $rd["wishes"],
                "test" => "20",
                "contact_preference" => "sms",
            ]);
            $client->waitForReload(function (Browser $browser) {
                $browser->clickAtXPath(self::x("class", "button", "Zatwierdź"));
            })
                ->assertSee("Zapytanie zostało pomyślnie dodane");

            $this->openArchmageDashboard($archmage);
            $archmage->with('.section[data-title="Zapytania"]', fn ($section) =>
                $section->assertSee($rd["title"])
                    ->assertSee("nowe")
            )
                ->waitForReload(function (Browser $browser) use ($rd) {
                    $browser->click('[role="model-card"][data-model="'.$rd["title"].' dla: '.$rd["client_name"].'"] .button[data-tippy="Szczegóły"]');
                })
                ->assertSee(implode(" – ", [$rd["artist"], $rd["title"]]));
            $this->fillOutRequestForArchmage($archmage, [
                "genre_id" => "folk",
                "price_code" => $rd["price_code"],
                "deadline" => $rd["deadline"]->format("d.m.Y"),
            ]);
            $archmage->waitFor("#price-summary table")
                ->assertSeeIn("#price-summary", $rd["price"])
                ->assertValueIsNot("#deadline", "");
            $archmage->waitForReload(function (Browser $browser) {
                $browser->click('.button[data-tippy="Oddaj"]');
            })
                ->assertSee("wycena do akceptacji");

            $request = Request::firstWhere([
                ["title", $rd["title"]],
                ["client_name", $rd["client_name"]],
            ]);

            $client->visitRoute("request", ["id" => $request->id])
                ->assertSee("wycena do akceptacji")
                ->assertSee($rd["title"])
                ->assertSee("Poproś o zmiany do utworu");
            $client->clickAtXPath(self::x("class", "button", "Poproś o zmiany do utworu"))
                ->waitFor("#modal-card")
                ->with("#modal-card", fn ($modal) => $modal
                    ->type("comment", "Przydałoby się jednak w d-moll")
                );
            $client->waitForReload(function (Browser $browser) {
                $browser->clickAtXPath(self::x("class", "button", "Zatwierdź"));
            })
                ->assertSee("wycena zakwestionowana");
        });
    }

    public function test_anon_can_ask_for_priority(): void
    {
        $this->browse(function(Browser $client, Browser $archmage) {
            $rd = self::getRequestData()["anon_impatient"];

            $this->openPodkladyModal($client, [
                "client_name" => $rd["client_name"],
                "email" => "iamimpatient@test.test",
                "phone" => "123778927",
                "title" => $rd["title"],
                "artist" => $rd["artist"],
                "link" => $rd["link"],
                "wishes" => $rd["wishes"],
                "test" => "20",
            ]);
            $client->waitForReload(function (Browser $browser) {
                $browser->clickAtXPath(self::x("class", "button", "Zatwierdź"));
            })
                ->assertSee("Zapytanie zostało pomyślnie dodane");

            $this->openArchmageDashboard($archmage);
            $archmage->with('.section[data-title="Zapytania"]', fn ($section) =>
                $section->assertSee($rd["title"])
                    ->assertSee("nowe")
            )
                ->waitForReload(function (Browser $browser) use ($rd) {
                    $browser->click('[role="model-card"][data-model="'.$rd["title"].' dla: '.$rd["client_name"].'"] .button[data-tippy="Szczegóły"]');
                })
                ->assertSee(implode(" – ", [$rd["artist"], $rd["title"]]));
            $this->fillOutRequestForArchmage($archmage, [
                "genre_id" => "songwriter",
                "wishes" => "z linią melodyczną",
                "price_code" => $rd["price_code"],
                "deadline" => $rd["deadline"]->format("d.m.Y"),
            ]);
            $archmage->waitFor("#price-summary table")
                ->assertSeeIn("#price-summary", $rd["price"])
                ->assertValueIsNot("#deadline", "");
            $archmage->waitForReload(function (Browser $browser) {
                $browser->click('.button[data-tippy="Oddaj"]');
            })
                ->assertSee("wycena do akceptacji");

            $request = Request::firstWhere([
                ["title", $rd["title"]],
                ["client_name", $rd["client_name"]],
            ]);

            $client->visitRoute("request", ["id" => $request->id])
                ->assertSee("wycena do akceptacji")
                ->assertSee($rd["title"])
                ->assertSee("Poproś o szybszą realizację");
            $client->clickAtXPath(self::x("class", "button", "Poproś o szybszą realizację"))
                ->with(".card[data-title='Termin realizacji']", fn ($card) => $card
                    ->assertSee("Przyspieszony termin realizacji")
                    ->assertSee("Wróć do poprzedniej wyceny")
                );
            $client->clickAtXPath(self::x("class", "button", "Kliknij tutaj, aby potwierdzić warunki zlecenia"))
                ->waitFor("#modal-card")
                ->with("#modal-card", fn ($modal) => $modal
                    ->assertSee("Zaznacz poniższe zgody")
                    ->assertSee("Zapłacę kwotę w wysokości ".($rd["price"] * 2))
                    ->assertSee("dostęp do plików otrzymam ".get_next_working_day()->format("d.m.Y"))
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

    public function test_anon_can_reject_request(): void
    {
        $this->browse(function(Browser $client, Browser $archmage) {
            $rd = self::getRequestData()["anon_unhappy"];

            $this->openPodkladyModal($client, [
                "client_name" => $rd["client_name"],
                "phone" => "123778928",
                "title" => $rd["title"],
                "artist" => $rd["artist"],
                "link" => $rd["link"],
                "test" => "20",
                "contact_preference" => "sms",
            ]);
            $client->waitForReload(function (Browser $browser) {
                $browser->clickAtXPath(self::x("class", "button", "Zatwierdź"));
            })
                ->assertSee("Zapytanie zostało pomyślnie dodane");

            $this->openArchmageDashboard($archmage);
            $archmage->with('.section[data-title="Zapytania"]', fn ($section) =>
                $section->assertSee($rd["title"])
                    ->assertSee("nowe")
            )
                ->waitForReload(function (Browser $browser) use ($rd) {
                    $browser->click('[role="model-card"][data-model="'.$rd["title"].' dla: '.$rd["client_name"].'"] .button[data-tippy="Szczegóły"]');
                })
                ->assertSee(implode(" – ", [$rd["artist"], $rd["title"]]));
            $this->fillOutRequestForArchmage($archmage, [
                "genre_id" => "songwriter",
                "price_code" => $rd["price_code"],
                "deadline" => $rd["deadline"]->format("d.m.Y"),
            ]);
            $archmage->waitFor("#price-summary table")
                ->assertSeeIn("#price-summary", $rd["price"])
                ->assertValueIsNot("#deadline", "");
            $archmage->waitForReload(function (Browser $browser) {
                $browser->click('.button[data-tippy="Oddaj"]');
            })
                ->assertSee("wycena do akceptacji");

            $request = Request::firstWhere([
                ["title", $rd["title"]],
                ["client_name", $rd["client_name"]],
            ]);

            $client->visitRoute("request", ["id" => $request->id])
                ->assertSee("wycena do akceptacji")
                ->assertSee($rd["title"])
                ->assertSee("lub tutaj, aby zrezygnować");
            $client->clickAtXPath(self::x("class", "button", "...lub tutaj, aby zrezygnować"))
                ->waitFor("#modal-card");
            $client->waitForReload(function (Browser $browser) {
                $browser->clickAtXPath(self::x("class", "button", "Zatwierdź"));
            })
                ->assertSee("wycena odrzucona")
                ->assertSee("Odnów");
        });
    }

    public function test_anon_sees_delayed_payment_info(): void
    {
        $this->browse(function(Browser $client, Browser $archmage) {
            $rd = self::getRequestData()["anon_with_delay"];

            $this->openPodkladyModal($client, [
                "client_name" => $rd["client_name"],
                "email" => "delay@test.test",
                "phone" => "123778928",
                "title" => $rd["title"],
                "artist" => $rd["artist"],
                "link" => $rd["link"],
                "test" => "20",
            ]);
            $client->waitForReload(function (Browser $browser) {
                $browser->clickAtXPath(self::x("class", "button", "Zatwierdź"));
            })
                ->assertSee("Zapytanie zostało pomyślnie dodane");

            $this->openArchmageDashboard($archmage);
            $archmage->with('.section[data-title="Zapytania"]', fn ($section) =>
                $section->assertSee($rd["title"])
                    ->assertSee("nowe")
            )
                ->waitForReload(function (Browser $browser) use ($rd) {
                    $browser->click('[role="model-card"][data-model="'.$rd["title"].' dla: '.$rd["client_name"].'"] .button[data-tippy="Szczegóły"]');
                })
                ->assertSee(implode(" – ", [$rd["artist"], $rd["title"]]));
            $this->fillOutRequestForArchmage($archmage, [
                "genre_id" => "soul",
                "price_code" => $rd["price_code"],
                "deadline" => $rd["deadline"]->format("d.m.Y"),
                "delayed_payment" => $rd["delayed_payment"]->format("d.m.Y"),
            ]);
            $archmage->waitFor("#price-summary table")
                ->assertSeeIn("#price-summary", $rd["price"])
                ->assertValueIsNot("#deadline", "");
            $archmage->waitForReload(function (Browser $browser) {
                $browser->click('.button[data-tippy="Oddaj"]');
            })
                ->assertSee("wycena do akceptacji");

            $request = Request::firstWhere([
                ["title", $rd["title"]],
                ["client_name", $rd["client_name"]],
            ]);

            $client->visitRoute("request", ["id" => $request->id])
                ->assertSee("wycena do akceptacji")
                ->assertSee("Jest kilka rzeczy, z którymi musisz się koniecznie zapoznać")
                ->assertSee($rd["title"])
                ->assertSeeIn(".card[data-title='Płatność']", "proszę o dokonanie wpłaty nie wcześniej niż ".$rd["delayed_payment"]->format("d.m.Y"));
            $client->clickAtXPath(self::x("class", "button", "Kliknij tutaj, aby potwierdzić warunki zlecenia"))
                ->waitFor("#modal-card")
                ->with("#modal-card", fn ($modal) => $modal
                    ->assertSee("Zaznacz poniższe zgody")
                    ->assertSee("Wpłaty dokonam nie wcześniej niż ".$rd["delayed_payment"]->format("d.m.Y"))
                )
                ->check("confirm_song")
                ->check("confirm_price")
                ->check("confirm_deadline")
                ->check("confirm_delayed_payment");
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
    private function openPodkladyModal(Browser $browser, ?array $fill_out_fields = null)
    {
        $browser->visit("/")
            ->waitForText("Podkłady i nuty")
            ->clickAtXPath(self::x("role", "service-button", "Podkłady i nuty"))
            ->waitForText("Złóż zapytanie")
            ->clickAtXPath(self::x("class", "button", "Złóż zapytanie"))
            ->waitFor("#modal-card");

        $this->fillOutPodkladyModal($browser, $fill_out_fields);
    }

    private function fillOutPodkladyModal(Browser $browser, ?array $fill_out_fields = null)
    {
        foreach ($fill_out_fields ?? [] as $field_name => $value) {
            if (in_array($field_name, [
                "contact_preference",
            ])) {
                $this->select($browser, "#$field_name", $value);
            } else {
                $browser->type($field_name, $value);
            }
        }
    }

    private function openArchmageDashboard(Browser $browser)
    {
        $this->openClientDashboard($browser, 1);
    }

    private function openClientDashboard(Browser $browser, int $user_id)
    {
        $browser->loginAs($user_id)
            ->visit("/profile");
    }

    private function select(Browser $browser, string $input_selector, mixed $value)
    {
        $browser->click(".choices:has($input_selector)")
            ->withKeyboard(fn (Keyboard $kbd) => $kbd->type($value)->press(WebDriverKeys::ENTER));
    }

    private function fillOutRequestForArchmage(Browser $browser, array $fill_out_fields)
    {
        foreach ($fill_out_fields ?? [] as $field_name => $value) {
            if (in_array($field_name, [
                "genre_id",
            ])) {
                $this->select($browser, "#$field_name", $value);
            } else {
                if ($value === null) {
                    $browser->clear($field_name);
                } else {
                    $browser->type($field_name, $value);
                }
            }
        }
    }
    #endregion
}
