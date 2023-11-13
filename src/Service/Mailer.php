<?php
namespace App\Service;

use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

# @author Jean Mermoz Effi <mangoua.effi@uvci.edu.ci>
# @description "Ce service est en charge de l'envoi de mail (un ou plusieur)"
class Mailer
{


    public function __construct
    (
        private MailerInterface $mailer,
    )
    {
    }

    /**
     * Undocumented function
     *
     * @param mixed $mails
     * @param string $subject
     * @param mixed $content
     * @param string $template
     * @param string|null $fromMail
     * @param string|null $senderName
     *
     * @return void Description " En charge de l'envoi de mail"
     */
    public function send($mails, string $subject, $content = null, string $template = null, string $fromMail = null, string $senderName = null)
    {
        if ($content === null && $template === null) 
        {
            return; // Ne rien faire et sortir de la méthode
        }

        $toAddresses = [];

        foreach ($mails as $mail)
        {
            $mailTo = new Address($mail);
            $toAddresses[] = $mailTo;
        }

        $fromMail = $fromMail ?? 'erp.notif@kuyopipeline.com';
        $senderName  = $senderName ?? 'KPL';
        
        $email = (new TemplatedEmail())
            ->from(new Address($fromMail, $senderName))
            ->to(...$toAddresses)
            ->subject($subject);

        if ($content !== null)
        {
            $email->html($content);
        }

        if ($template !== null)
        {
            $email->context([
                // 'param' => $paramData;
            ]);
            $email->htmlTemplate($template);
        }
           
        $this->mailer->send($email);
    }
}