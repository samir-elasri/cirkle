<?php

namespace App\Mail;

use App\Models\Core\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminMail extends Mailable
{
	use Queueable, SerializesModels;

	private $text;

	/**
	 * @var null
	 */
	private $fromName;

	/**
	 * @var null
	 */
	private $fromMail;

	/**
	 * Create a new message instance.
	 *
	 * @param $text
	 * @param $subject
	 * @param  null  $fromMail
	 * @param  null  $fromName
	 */
	public function __construct($text, $subject, $fromMail = null, $fromName = null)
	{
		$this->text = $text;
		$this->subject = $subject;
		$this->fromMail = $fromMail;
		$this->fromName = $fromName;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		$setting = setting();
		$from = $this->fromMail ?? $setting->sender_email ?? 'mbiance@mbiance.com';
		$fromName = $this->fromName ?? $setting->sender_email_name ?? 'mbiance';
		$footer = $setting->email_footer_text ?? '';

		return $this->markdown('emails.adminMail')
			->from($from, $fromName)
			->with('text', $this->text)
			->with('footer', $footer);
	}
}
