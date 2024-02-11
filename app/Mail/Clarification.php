<?php

namespace App\Mail;

use App\Models\Quest;
use App\Models\Request;
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
    public function __construct($data)
    {
        if(is_string($data)){
            $this->is_request = strlen($data) == 36;
            $this->re_quest = ($this->is_request) ? Request::findOrFail($data) : Quest::findOrFail($data);
        }else{
            $this->re_quest = $data;
            $this->is_request = strlen($this->re_quest->id) == 36;
        }
        $this->pl = client_polonize($this->is_request ? $this->re_quest->client_name : $this->re_quest->client->client_name);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Prośba o doprecyzowanie | ".($this->is_request ? $this->re_quest->full_title : $this->re_quest->song->full_title))
            ->view('emails.clarification');
    }
}
