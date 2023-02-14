<?php

namespace App\Mail;

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
    public $pl;
    public function __construct($quest, $reason)
    {
        $this->quest = $quest;
        $this->reason = $reason;
        $this->pl = client_polonize($quest->client->client_name);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Zmiana warunków zlecenia | ".($this->quest->song->title ?? "utwór bez tytułu"))
            ->view('emails.quest-requoted');
    }
}
