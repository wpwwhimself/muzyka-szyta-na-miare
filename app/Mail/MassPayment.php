<?php

namespace App\Mail;

use App\Models\Quest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MassPayment extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $quests;
    public $pl;
    public function __construct($quests)
    {
        $this->quests = collect(is_string($quests) ? explode(";", $quests) : $quests)->map(fn($el) => is_string($el) ? Quest::findOrFail($el) : $el);
        $this->pl = client_polonize($this->quests[0]->user->display_name);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Wpłata zarejestrowana")
            ->view('emails.mass-payment', ["title" => "Zlecenie zaktualizowane"]);
    }
}
