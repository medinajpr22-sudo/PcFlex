<?php

namespace App\Mail;

use App\Models\Services;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DevolucionComprobante extends Mailable
{
    use Queueable, SerializesModels;

    public $service;
    public $usuario;
    public $equipo;

    /**
     * Create a new message instance.
     */
    public function __construct($service, $usuario, $equipo)
    {
        $this->service = $service;
        $this->usuario = $usuario;
        $this->equipo = $equipo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Devolución Exitosa! - Comprobante de Devolución',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.devolucion-comprobante',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
