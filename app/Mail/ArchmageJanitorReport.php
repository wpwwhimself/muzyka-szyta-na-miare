<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ArchmageJanitorReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $summary;
    public function __construct($summary)
    {
        $this->summary = $summary;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('kontakt@muzykaszytanamiare.pl', 'Goniec MSZNM')
            ->subject("Raport Sprzątacza")
            ->view('emails.archmage-janitor-report');
    }
}
