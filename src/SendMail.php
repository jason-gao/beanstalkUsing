<?php

namespace beanstalkUsing;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class SendMail
{
    private $mail;
    private $error = '';

    public function __construct($config)
    {
        $mail = new PHPMailer(true);                                                                     // Passing `true` enables exceptions
        //Server settings
        $mail->SMTPDebug = isset($config['SMTPDebug']) ? $config['SMTPDebug'] : 2;                                 // Enable verbose debug output
        $mail->set('CharSet', 'utf-8');
        $mail->isSMTP();                                                                                           // Set mailer to use SMTP
        $mail->Host       = isset($config['Host']) ? $config['Host'] : 'smtp1.example.com;smtp2.example.com';      // Specify main and backup SMTP servers
        $mail->SMTPAuth   = isset($config['SMTPAuth']) ? $config['SMTPAuth'] : true;                               // Enable SMTP authentication
        $mail->Username   = isset($config['Username']) ? $config['Username'] : 'user@example.com';                 // SMTP username
        $mail->Password   = isset($config['Password']) ? $config['Password'] : 'secret';                           // SMTP password
        $mail->SMTPSecure = isset($config['SMTPSecure']) ? $config['SMTPSecure'] : 'tls';                          // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = isset($config['Port']) ? $config['Port'] : 587;                                        // TCP port to connect to
        $this->mail       = $mail;
    }

    public function sendMail($from, $fromName, $to, $Subject, $Body, $AltBody = '')
    {
        try {
            //Recipients
            $this->mail->setFrom($from, $fromName);
            $this->mail->clearAddresses();
            foreach ($to as $t) {
                $this->mail->addAddress($t);               // Name is optional
            }

            //Content
            $this->mail->isHTML(true);              // Set email format to HTML
            $this->mail->Subject = $Subject;
            $this->mail->Body    = $Body;
            $this->mail->AltBody = $AltBody;

            $this->mail->send();
            $this->error = '';
            return true;
        } catch (Exception $e) {
            $this->error = 'Message could not be sent. Mailer Error: ' . $this->mail->ErrorInfo;
            return false;
        }
    }


    public function getError()
    {
        return $this->error;
    }
}