<?php

namespace App\Mail;

use App\Models\Quest;
use App\Models\Request;
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
    public function __construct($data)
    {
        if(is_string($data)){
            $this->isRequest = strlen($data) == 36;
            $this->quest = ($this->isRequest) ? Request::findOrFail($data) : Quest::findOrFail($data);
        }else{
            $this->quest = $data;
            $this->isRequest = strlen($this->quest->id) == 36;
        }
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
            ->subject("[".($this->quest->is_priority ? "PRIORYTETOWO " : "").$this->quest->status->status_name."] ".$this->quest->full_title)
            ->replyTo($this->quest->email ?? $this->quest->client?->email, $this->quest->name ?? $this->quest->client?->name)
            ->view('emails.archmage-quest-mod', ["title" => "Goniec przynosi wieści"])
            :
            $this
            ->from('kontakt@muzykaszytanamiare.pl', 'Goniec MSZNM')
            ->subject("[".$this->quest->status->status_name."] ".$this->quest->id." | ".$this->quest->song->full_title)
            ->replyTo($this->quest->client->email, $this->quest->client->name)
            ->view('emails.archmage-quest-mod', ["title" => "Goniec przynosi wieści"]);
    }
}
