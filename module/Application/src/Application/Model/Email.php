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

    public function sendTemplate($id, $template, $vars = array()) {
        $temp = array();
        try {
            $temp = $this->getTemplate($template, $vars);
        } catch (\Exception $e) {
            return false;
        }
        return $this->sendTo($id, $temp['subject'], $temp['template']);
    }

    public function getTemplate($name, $vars = array()) {
        $view = new \Zend\View\Renderer\PhpRenderer();
        $view->setResolver(new \Zend\View\Resolver\TemplatePathStack(array('script_paths' => array(__DIR__ . '/Emails/'))));
        $view->setVars($vars);
        
        $result = $view->render($name);
        $subject = $view->subject;

        return array('template' => $result, 'subject' => $subject);
    }

    public function sendTo($id, $subject, $text) {
        $validator = new \Zend\Validator\EmailAddress();
        if ($validator->isValid($id)) {
            return $this->sendToMail($id, $subject, $text);
        } else if (is_int($id) && (int) $id > 0) {
            return $this->sendToUser($id, $subject, $text);
        }
        return false;
    }

    public function sendToUser($id, $subject, $text) {
        if (($user = $this->user->getUser($id)) === false) {
            return false;
        }
        return $this->sendMail($user->email, $this->from, $this->subject . $subject, $text);
    }

    public function sendToMail($mail, $subject, $text) {
        return $this->sendMail($mail, $this->from, $this->subject . $subject, $text);
    }

    public function sendMail($to, $from, $subject, $text) {
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