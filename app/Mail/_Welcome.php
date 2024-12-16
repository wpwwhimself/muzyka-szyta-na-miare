<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class _Welcome extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $pl;
    public $client;
    public function __construct($params)
    {
        list($client_id) = $params;
        $this->client = User::findOrFail($client_id);
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
            ->subject("Nowa strona internetowa i logowanie")
            ->view('emails._welcome', ["title" => "Witam na stronie " . env("APP_NAME")]);
    }
}
