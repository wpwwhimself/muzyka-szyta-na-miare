<?php

namespace App\Mail;

use App\Models\Quest;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReceived extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $quest;
    public $paymentShouldBeDelayed;
    public $pl;
    public function __construct($data)
    {
        $this->quest = is_string($data) ? Quest::findOrFail($data) : $data;
        $this->pl = client_polonize($this->quest->client->client_name);
        $this->paymentShouldBeDelayed = $this->quest->delayed_payment?->gt(Carbon::today());
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(($this->paymentShouldBeDelayed ? "Przedwczesna wpłata" : "Wpłata")." zarejestrowana za zlecenie ".$this->quest->id)
            ->view('emails.payment-received', ["title" => "Otrzymano wpłatę"]);
    }
}
