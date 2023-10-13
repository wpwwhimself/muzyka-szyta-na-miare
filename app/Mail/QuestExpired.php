<?php

namespace App\Mail;

use App\Models\Quest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuestExpired extends Mailable
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
        $this->quest = is_string($quest) ? Quest::findOrFail($quest) : $quest;
        $this->reason = $reason;
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
            ->subject("Wygaszono zlecenie nr ".$this->quest->id)
            ->view('emails.quest-expired');
    }
}
