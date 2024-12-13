<?php

namespace App\Mail;

use App\Models\Quest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuestRequoted extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $quest;
    public $reason;
    public $price_difference;
    public $pl;
    public function __construct($quest, $reason, $price_difference)
    {
        $this->quest = is_string($quest) ? Quest::findOrFail($quest) : $quest;
        $this->reason = $reason;
        $this->price_difference = $price_difference;
        $this->pl = client_polonize($this->quest->client->client_name);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Zmiana warunków zlecenia | ".$this->quest->song->full_title)
            ->view('emails.quest-requoted', ["title" => "Zmieniona wycena zlecenia"]);
    }
}
