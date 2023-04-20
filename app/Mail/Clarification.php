<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Clarification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $re_quest;
    public $is_request;
    public $pl;
    public function __construct($re_quest)
    {
        $this->re_quest = $re_quest;
        $this->is_request = strlen($re_quest->id) == 36;
        $this->pl = client_polonize($this->is_request ? $re_quest->client_name : $re_quest->client->client_name);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Prośba o doprecyzowanie | ".(($this->is_request ? $this->re_quest->title : $this->re_quest->song->title) ?? "utwór bez tytułu"))
            ->view('emails.clarification');
    }
}
