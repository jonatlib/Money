<?php

namespace Application\Model;
use \Zend\Mail;

class Email {    
    protected $mail;
    protected $from;
    protected $subject;
    /**
     * @var \Application\Model\User
     */
    protected $user;
    
    public function sendToUser($id, $subject, $text){
        if( ($user = $this->user->getUser($id)) === false ){
            return false;
        }
        return $this->sendMail($user->email, $this->from, $this->subject . $subject, $text);
    }
    
    public function sendToMail($mail, $subject, $text){
        return $this->sendMail($mail, $this->from, $this->subject . $subject, $text);
    }
    
    public function sendMail($to, $from, $subject, $text){
        $message = new Mail\Message();
        $message->setEncoding("UTF-8");
        
        $message->setTo($to);
        $message->setFrom($from);
        $message->setSender($from);
        $message->setSubject($subject);
        $message->setBody($text);
        
        return $this->mail->send($message);
    }
    
    public function __construct($from, $subject, $adapter) {
        $this->mail = new \Zend\Mail\Transport\Sendmail();
        $this->user = new \Application\Model\User($adapter);
        $this->from = $from;
        $this->subject = $subject;
    }
    
}