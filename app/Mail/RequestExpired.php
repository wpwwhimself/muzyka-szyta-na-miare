<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequestExpired extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $request;
    public $pl;
    public function __construct($request)
    {
        $this->request = $request;
        $this->pl = client_polonize($request->client_name);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Wygaszono zapytanie | ".($this->request->title ?? "utwór bez tytułu"))
            ->view('emails.request-expired');
    }
}
