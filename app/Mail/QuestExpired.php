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
    public $treat_as;
    public $pl;
    public function __construct($quest, $reason)
    {
        $this->quest = is_string($quest) ? Quest::findOrFail($quest) : $quest;
        $this->reason = $reason;
        $this->treat_as = ($this->quest->status_id === 19 && $this->quest->paid) ? "zaakceptowane" : "odrzucone";
        $this->pl = client_polonize($this->quest->user->notes->client_name);
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
            ->view('emails.quest-expired', ["title" => "Zlecenie wygaszone"]);
    }
}
