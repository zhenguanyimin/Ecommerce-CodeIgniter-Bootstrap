<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require 'PHPMailer/PHPMailerAutoload.php';

class SendMail
{

    public $mail;

    public function __construct()
    {
		$this->mail = new PHPMailer; 
		$this->mail->isSMTP(); 
		$this->mail->SMTPDebug = 0; 
		$this->mail->Debugoutput = 'html'; 
		$this->mail->Host = 'smtp.qq.com'; 
		$this->mail->Port = 465; 
		$this->mail->SMTPSecure = 'ssl'; 
		$this->mail->SMTPAuth = true; 
		$this->mail->Username = "670518913@qq.com"; 
		$this->mail->Password = "grnlvspoiowsbegj";
		$this->mail->CharSet = 'UTF-8';
    }

    public function clearAddresses()
    {
        if(method_exists($this->mail, 'clearAddresses')) {
            $this->mail->clearAddresses();
        }
    }

    public function sendTo($toEmail, $recipientName, $subject, $msg)
    {
        $this->mail->setFrom('670518913@qq.com', 'BoomkMarket');
        $this->mail->addAddress($toEmail, $recipientName);
        //$this->mail->isHTML(true); 
        $this->mail->Subject = $subject;
        $this->mail->Body = $msg;
        if (!$this->mail->send()) {
            log_message('error', 'Mailer Error: ' . $this->mail->ErrorInfo);
            return false;
        }
        return true;
    }

}
