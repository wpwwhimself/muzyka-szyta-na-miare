<?php

namespace App\Mail;

use App\Models\StatusChange;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ArchmageQuestMod extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $quest;
    public $isRequest;
    public function __construct($quest)
    {
        $this->quest = $quest;
        $this->isRequest = strlen($quest->id) == 36;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return ($this->isRequest) ?
            $this
            ->from('kontakt@muzykaszytanamiare.pl', 'Goniec MSZNM')
            ->subject("[MSZNM] [".$this->quest->status->status_name."] ".$this->quest->title)
            ->view('emails.archmage-quest-mod')
            :
            $this
            ->from('kontakt@muzykaszytanamiare.pl', 'Goniec MSZNM')
            ->subject("[MSZNM] [".$this->quest->status->status_name."] ".$this->quest->id." | ".$this->quest->song->title)
            ->view('emails.archmage-quest-mod');
    }
}
