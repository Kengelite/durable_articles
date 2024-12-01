<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RepairStatusUpdateNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;

    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    public function build()
    {
        return $this->subject('แจ้งเตือนอัปเดตสถานะการซ่อม')
            ->view('emails.repair_status_update')
            ->with('emailData', $this->emailData);
    }
}
