<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendMailActivation extends Mailable
{
    use Queueable, SerializesModels;
    protected $url;
    protected $id;
    protected $random;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $url, $id, $random)
    {
        //
        $this->url = $url;
        $this->id = $id;
        $this->random = $random;
    }
   

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Confirmation Email',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'email',
            with: [
               
                'url'  => $this->url,
                'random' => $this->random,
                'id' => $this->id->id,
                
                
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
