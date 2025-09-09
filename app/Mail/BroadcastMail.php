<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $content;

    /**
     * Buat instance email
     */
    public function __construct($subjectLine, $content)
    {
        $this->subjectLine = $subjectLine;
        $this->content = $content;
    }

    /**
     * Build email
     */
    public function build()
    {
        // HTML langsung tanpa blade
        $html = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
                    .container { max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
                    h2 { color: #333; margin-bottom: 15px; }
                    p { font-size: 14px; color: #555; line-height: 1.6; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>" . e($this->subjectLine) . "</h2>
                    <p>" . nl2br(e($this->content)) . "</p>
                </div>
            </body>
            </html>
        ";

        return $this->subject($this->subjectLine)
                    ->html($html);
    }
}