<?php

namespace App\Mail;

use App\Models\Quest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReturned extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $quest;
    public $pl;
    public function __construct($data)
    {
        $this->quest = is_string($data) ? Quest::findOrFail($data) : $data;
        $this->pl = client_polonize($this->quest->user->notes->client_name);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Zwrot wpłaty za zlecenie ".$this->quest->id)
            ->view('emails.payment-returned', ["title" => "Zwrócono wpłatę"]);
    }
}
