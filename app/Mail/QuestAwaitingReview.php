<?php

namespace App\Mail;

use App\Models\Quest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuestAwaitingReview extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $quest;
    public $pl;
    public function __construct($quest)
    {
        $this->quest = is_string($quest) ? Quest::findOrFail($quest) : $quest;
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
            ->subject("Zlecenie nr ".$this->quest->id." oczekuje na opinię")
            ->view('emails.quest-awaiting-review', ["title" => "Zlecenie oczekuje na opinię"]);
    }
}
