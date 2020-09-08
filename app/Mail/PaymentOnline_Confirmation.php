<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentOnline_Confirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $subject_line;
    public $payer;
    public $charge;

    /**
     * Create a new message instance.
     *
     * @param User $payer
     * @param $charge
     * @param null $subject
     */
    public function __construct(User $payer, $charge, $subject = null)
    {
        $this->subject_line = $subject ?: '[' . config('site.community_acronym') . '] Payment Acknowledgement for ' . $payer->name;
        $this->payer = $payer;
        $this->charge = $charge;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject_line)
            ->replyTo(config('site.email-finance-mailbox', config('site.email_general')), config('site.community_acronym') . ' Finance')
            ->markdown('emails.online_payment_acknowledgement')
            ;
    }
}
