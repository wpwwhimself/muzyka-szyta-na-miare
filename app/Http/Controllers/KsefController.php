<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\Random;
use SimpleXMLElement;

class KsefController extends Controller
{
    private string $URL;
    private string $QRURL;

    public function __construct()
    {
        $this->URL = env("KSEF_URL", "https://api-test.ksef.mf.gov.pl/v2");
        $this->QRURL = env("KSEF_QR_URL", "https://qr-test.ksef.mf.gov.pl/invoice");
    }

    #region invoices
    public function exportInvoice(Invoice $invoice) {
        $this->authenticate();

        //? prepare keys
        $sym_key = Random::string(32);
        $iv = Random::string(16);

        //? encrypt key
        $public_key = Http::get($this->URL . "/security/public-key-certificates")
            ->collect()
            ->firstWhere(fn ($k) => $k["usage"] == ["SymmetricKeyEncryption"])
            ["certificate"];
        $public_key = PublicKeyLoader::load($public_key);
        $enc_sym_key = $public_key->encrypt($sym_key);
        $enc_sym_key = base64_encode($enc_sym_key);
        $enc_iv = base64_encode($iv);

        //? open session
        $session = Http::withToken(session("ksef_token"))
            ->post($this->URL . "/sessions/online", [
                "formCode" => [
                    "systemCode" => "FA (3)",
                    "schemaVersion" => "1-0E",
                    "value" => "FA",
                ],
                "encryption" => [
                    "encryptedSymmetricKey" => $enc_sym_key,
                    "initializationVector" => $enc_iv,
                ],
            ])->json();

        //? send invoice
        try {
            $prepared_invoice = $this->prepareInvoiceAsXml($invoice)->asXML();
            $cipher = new AES("cbc");
            $cipher->setKey($sym_key);
            $cipher->setIV($iv);
            $encrypted_invoice = $cipher->encrypt($prepared_invoice);

            $send_data = Http::withToken(session("ksef_token"))
                ->post($this->URL . "/sessions/online/" . $session["referenceNumber"] . "/invoices", [
                    "invoiceHash" => base64_encode(hash("sha256", $prepared_invoice, true)),
                    "invoiceSize" => strlen($prepared_invoice),
                    "encryptedInvoiceHash" => base64_encode(hash("sha256", $encrypted_invoice, true)),
                    "encryptedInvoiceSize" => strlen($encrypted_invoice),
                    "encryptedInvoiceContent" => base64_encode($encrypted_invoice),
                ])->json();

            //? is it ok?
            for ($try = 0; $try < 5; $try++) {
                $send_status = Http::withToken(session("ksef_token"))
                    ->get($this->URL . "/sessions/" . $session["referenceNumber"] . "/invoices/" . $send_data["referenceNumber"])
                    ->json();

                $response = $send_status["status"]["code"];
                if ($response < 200) {
                    sleep(1);
                    continue;
                }

                if ($response != 200) {
                    throw new \Exception("KSeF: Cannot authenticate: " . json_encode($response));
                }

                $send_data = $send_status;

                break;
            }
        } catch (\Throwable $th) {
            $send_data = ["msg" => $th->getMessage()];
            Log::error($th, $send_status["status"]);
        }

        //? close session
        $session_closed = Http::withToken(session("ksef_token"))
            ->post($this->URL . "/sessions/online/" . $session["referenceNumber"] . "/close")
            ->status() == 204;

        if ($send_data["ksefNumber"] ?? false) {
            $invoice->update([
                "ksef_number" => $send_data["ksefNumber"],
                "ksef_link" => implode("/", [
                    $this->QRURL,
                    "9950259579",
                    $invoice->created_at->format("d-m-Y"),
                    Str::of(base64_encode(hash("sha256", $prepared_invoice, true)))
                        ->replace("_", "-")
                        ->replace(["+", "/"], "_")
                        ->replace("=", ""),
                ]),
            ]);
        } else {
            return back()->with("toast", ["error", "Coś poszło nie tak, sprawdź logi"]);
        }

        return back()->with("toast", ["success", "Faktura wysłana"]);
    }

    private function prepareInvoiceAsXml(Invoice $invoice): SimpleXMLElement {
        $prepared_invoice = new SimpleXMLElement('<Faktura xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://crd.gov.pl/wzor/2025/06/25/13775/"/>');

        $inv_header = $prepared_invoice->addChild("Naglowek");
        $code = $inv_header->addChild("KodFormularza", "FA");
        $code->addAttribute("kodSystemowy", "FA (3)");
        $code->addAttribute("wersjaSchemy", "1-0E");
        $inv_header->addChild("WariantFormularza", "3");
        $inv_header->addChild("DataWytworzeniaFa", Carbon::now()->toIso8601String());
        $inv_header->addChild("SystemInfo", "Aplikacja Muzyka Szyta Na Miarę");

        $seller = $prepared_invoice->addChild("Podmiot1");
        $seller_data = $seller->addChild("DaneIdentyfikacyjne");
        $seller_data->addChild("NIP", "9950259579");
        $seller_data->addChild("Nazwa", "Wojciech Przybyła - Muzyka Szyta Na Miarę");
        $seller_address = $seller->addChild("Adres");
        $seller_address->addChild("KodKraju", "PL");
        $seller_address->addChild("AdresL1", "Łąkie 62, 62-068 Łąkie");

        $buyer = $prepared_invoice->addChild("Podmiot2");
        $buyer_data = $buyer->addChild("DaneIdentyfikacyjne");
        $buyer_data->addChild("NIP", str_replace("-", "", $invoice->payer_nip));
        $buyer_data->addChild("Nazwa", implode(" ", array_filter([$invoice->payer_name, $invoice->payer_title])));
        $buyer_address = $buyer->addChild("Adres");
        $buyer_address->addChild("KodKraju", "PL");
        $buyer_address->addChild("AdresL1", $invoice->payer_address);
        $buyer->addChild("JST", 2);
        $buyer->addChild("GV", 2);

        if ($invoice->receiver_name) {
            $receiver = $prepared_invoice->addChild("Podmiot3");
            $receiver_data = $receiver->addChild("DaneIdentyfikacyjne");
            $receiver_data->addChild("NIP", str_replace("-", "", $invoice->receiver_nip));
            $receiver_data->addChild("Nazwa", implode(" ", array_filter([$invoice->receiver_name, $invoice->receiver_title])));
            $receiver_address = $receiver->addChild("Adres");
            $receiver_address->addChild("KodKraju", "PL");
            $receiver_address->addChild("AdresL1", $invoice->receiver_address);
            $receiver->addChild("Rola", 2);
        }

        $invoice_data = $prepared_invoice->addChild("Fa");
        $invoice_data->addChild("KodWaluty", "PLN");
        $invoice_data->addChild("P_1", $invoice->created_at->format("Y-m-d"));
        $invoice_data->addChild("P_2", $invoice->full_code);

        $totals = [
            "net" => $invoice->quests->reduce(fn ($c, $q) => $c + $q->data_for_invoice["net_price"], 0),
            "gross" => $invoice->quests->reduce(fn ($c, $q) => $c + $q->data_for_invoice["gross_price"], 0),
        ];
        $invoice_data->addChild("P_13_1", $totals["net"]);
        $invoice_data->addChild("P_14_1", $totals["gross"] - $totals["net"]);
        $invoice_data->addChild("P_15", $totals["gross"]);

        $annotations = $invoice_data->addChild("Adnotacje");
            $annotations->addChild("P_16", "2");
            $annotations->addChild("P_17", "2");
            $annotations->addChild("P_18", "2");
            $annotations->addChild("P_18A", "2");
            $annotations->addChild("Zwolnienie")->addChild("P_19N", "1");
            $annotations->addChild("NoweSrodkiTransportu")->addChild("P_22N", "1");
            $annotations->addChild("P_23", "2");
            $annotations->addChild("PMarzy")->addChild("P_PMarzyN", "1");
        $invoice_data->addChild("RodzajFaktury", "VAT");

        foreach ($invoice->quests as $i => $quest) {
            $row = $invoice_data->addChild("FaWiersz");
            $row->addChild("NrWierszaFa", $i + 1);
            $row->addChild("P_7", $quest->data_for_invoice["label"]);
            $row->addChild("P_8A", "szt.");
            $row->addChild("P_8B", 1);
            $row->addChild("P_9B", $quest->data_for_invoice["gross_price"]);
            $row->addChild("P_11A", $quest->data_for_invoice["gross_price"]);
            $row->addChild("P_12", $quest->data_for_invoice["vat_rate"] * 100);
        }

        $payment_data = $invoice_data->addChild("Platnosc");
        $payment_date_data = $payment_data->addChild("TerminPlatnosci")->addChild("TerminOpis");
            $payment_date_data->addChild("Ilosc", 14);
            $payment_date_data->addChild("Jednostka", "dni");
            $payment_date_data->addChild("ZdarzeniePoczatkowe", "od wystawienia faktury");
        $payment_data->addChild("FormaPlatnosci", 6);
        $payment_data->addChild("RachunekBankowy")->addChild("NrRB", "58 1090 1607 0000 0001 5333 1539");

        return $prepared_invoice;
    }
    #endregion

    #region auth
    private function authenticate() {
        //? challenge
        $challenge = Http::post($this->URL . "/auth/challenge", [])->json();

        //? encrypt token
        $token = env("KSEF_TOKEN") . "|" . $challenge["timestampMs"];
        $public_key = Http::get($this->URL . "/security/public-key-certificates")
            ->collect()
            ->firstWhere(fn ($k) => $k["usage"] == ["KsefTokenEncryption"])
            ["certificate"];
        $public_key = PublicKeyLoader::load($public_key);
        $token = $public_key->encrypt($token);
        $token = base64_encode($token);

        //? auth
        $reference = Http::post($this->URL . "/auth/ksef-token", [
            "challenge" => $challenge["challenge"],
            "contextIdentifier" => [
                "type" => "Nip",
                "value" => "9950259579",
            ],
            "encryptedToken" => $token,
        ])->json();

        //? am I authenticated?
        for ($try = 0; $try < 5; $try++) {
            $data = Http::withToken($reference["authenticationToken"]["token"])
                ->get($this->URL . "/auth/" . $reference["referenceNumber"])->json("status");

            $response = $data["code"];
            if ($response == 100) {
                sleep(1);
                continue;
            }

            if ($response != 200) {
                throw new \Exception("KSeF: Cannot authenticate: " . json_encode($data));
            }

            break;
        }

        //? redeem token
        $tokens = Http::withToken($reference["authenticationToken"]["token"])
            ->post($this->URL . "/auth/token/redeem")
            ->collect();

        session()->put("ksef_token", $tokens["accessToken"]["token"]);
        session()->put("ksef_refresh_token", $tokens["refreshToken"]["token"]);
    }

    private function getSymKey() {

    }
    #endregion
}
