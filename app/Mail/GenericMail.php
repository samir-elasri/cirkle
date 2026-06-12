<?php

namespace App\Mail;

use App\Models\Core\Setting;
use App\Models\Core\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericMail extends Mailable
{
    use Queueable, SerializesModels;

	private $text;

	/**
	 * @var Subscriber
	 */
	private $subscriber;

	/**
	 * Create a new message instance.
	 *
	 * @param  Subscriber  $subscriber
	 * @param $text
	 * @param $subject
	 */
    public function __construct(Subscriber $subscriber, $text, $subject, $replyTo = null)
    {
        $this->subscriber = $subscriber;
        $this->text = $text;
        $this->subject = $subject;

        if ($replyTo) {
            $this->replyTo = $replyTo;
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		$setting = setting();
		$from = $setting->sender_email ?? 'mbiance@mbiance.com';
		$fromName = $setting->sender_email_name ?? 'mbiance';
		$footer = $setting->email_footer_text ?? '';

        return $this->markdown('emails.genericMail')
			->from($from, $fromName)
			->with('subscriber', $this->subscriber)
			->with('text', $this->text)
			->with('footer', $footer);
    }
}
