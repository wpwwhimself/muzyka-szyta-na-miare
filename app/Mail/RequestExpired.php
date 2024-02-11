<?php

namespace App\Mail;

use App\Models\Request;
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
    public function __construct($data)
    {
        $this->request = is_string($data) ? Request::findOrFail($data) : $data;
        $this->pl = client_polonize($this->request->client_name);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Wygaszono zapytanie | ".$this->request->full_title)
            ->view('emails.request-expired');
    }
}
