<?php
namespace Sailor\Services;

use Sailor\Core\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();

        $this->mailer->SMTPDebug   = 2;
        $this->mailer->Host        = Config::get('email.HOST');
        $this->mailer->SMTPAuth    = true;
        $this->mailer->Username    = Config::get('email.USERNAME');
        $this->mailer->Password    = Config::get('email.PASSWORD');
        $this->mailer->SMTPSecure  = Config::get('email.ENCRYPTION');                                  
        $this->mailer->Port        = Config::get('email.PORT');
        $this->mailer->Charset     = Config::get('email.CHARSET');
        $this->mailer->setFrom(Config::get('email.FROM_ADDRESS'), Config::get('email.FROM_NAME'));
        $this->mailer->isHTML(true);
    }

    public function send($addresses, $subject, $message)
    {
        if (is_string($addresses)) {
            $addresses = [$addresses];
        }

        foreach ($addresses as $address) {
            $this->mailer->addAddress($address);
        }

        $this->mailer->Subject = '=?utf8?B?' . base64_encode($subject) . '?=';
        $this->mailer->Body = $message;
        $this->mailer->send();
    }
}
