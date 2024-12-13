<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PatronRejected extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $client;
    public $pl;
    public function __construct($data)
    {
        $this->client = is_string($data) ? Client::findOrFail($data) : $data;
        $this->pl = client_polonize($this->client->client_name);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Nie przyznano zniżki za reklamę")
            ->view('emails.patron-rejected', ["title" => "Nie przyznano zniżki za reklamę"]);
    }
}
