<?php

namespace App\Mail;

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
    public function __construct($quest)
    {
        $this->quest = $quest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('contact@wpww.pl', 'Goniec MSZNM')
            ->subject("[MSZNM] ".$this->quest->id." ".$this->quest->song->title." => ".$this->quest->status->status_name)
            ->view('emails.archmage-quest-mod');
    }
}
