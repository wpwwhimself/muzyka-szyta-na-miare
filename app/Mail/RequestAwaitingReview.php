<?php

namespace App\Mail;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequestAwaitingReview extends Mailable
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
        $this->request = is_string($request) ? Request::findOrFail($request) : $request;
        $this->pl = client_polonize($this->request->client?->client_name ?? $this->request->client_name);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Zapytanie oczekuje na opinię")
            ->view('emails.request-awaiting-review', ["title" => "Zapytanie oczekuje na opinię"]);
    }
}
