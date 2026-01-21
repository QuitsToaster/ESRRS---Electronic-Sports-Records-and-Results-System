<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ViewerFeedback extends Mailable
{
    use Queueable, SerializesModels;

    public $data; // feedback data

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('New Feedback from Viewer')
                    ->markdown('emails.viewer_feedback');
    }
}
