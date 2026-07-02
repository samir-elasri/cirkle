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
	 * Pièces jointes en mémoire : [['data' => ..., 'name' => ..., 'mime' => ...], …]
	 * (ex. la facture PDF du courriel de confirmation d'achat).
	 * NB : ne PAS nommer « rawAttachments » — propriété PUBLIQUE héritée de Mailable;
	 * la redéclarer en privé fatale à chaque envoi de courriel.
	 *
	 * @var array
	 */
	private $ckAttachments = [];

	/**
	 * Create a new message instance.
	 *
	 * @param  Subscriber  $subscriber
	 * @param $text
	 * @param $subject
	 */
    public function __construct(Subscriber $subscriber, $text, $subject, $replyTo = null, array $attachments = [])
    {
        $this->subscriber = $subscriber;
        $this->text = $text;
        $this->subject = $subject;
        $this->ckAttachments = $attachments;

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

        $mail = $this->markdown('emails.genericMail')
			->from($from, $fromName)
			->with('subscriber', $this->subscriber)
			->with('text', $this->text)
			->with('footer', $footer);

		foreach ($this->ckAttachments as $attachment) {
			$mail->attachData(
				$attachment['data'],
				$attachment['name'],
				['mime' => $attachment['mime'] ?? 'application/pdf']
			);
		}

		return $mail;
    }
}
