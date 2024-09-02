<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExampleMail extends Mailable
{
    use Queueable, SerializesModels;

    public $asunto;
    public $contenido;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($asunto, $contenido)
    {
        $this->asunto = $asunto;
        $this->contenido = $contenido;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->asunto)
            ->view('emails.correo_html')
            ->with([
                'photoUrl' => $this->contenido['photoUrl'],
                'fechaNotificacion' => $this->contenido['fechaNotificacion'],
            ])
            ->attach(public_path($this->contenido['photoPath']), [
                'as' => 'notification_image.jpg', // Nombre del archivo adjunto
                'mime' => 'image/jpeg', // Tipo MIME del archivo adjunto
            ]);
    }
    
    
}
