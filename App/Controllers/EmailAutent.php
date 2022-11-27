<?php

namespace App\Controllers;
use App\PHPMailer\PHPMailer;
//include_once 'App/Controllers/PHPMailer/PHPMailer/PHPMailer.php';
use Exception;
use stdClass;

class EmailAutent {
    
    private $mail;
    private $data;
    private $error;
    
    public function __construct() {
        $this->mail = new PHPMailer(true); //TRUE PARA SABER QUE PODE TER EXCESSÕES
        $this->data = new stdClass();
        $this->mail->isSMTP();
        $this->mail->isHTML();
        $this->mail->setLanguage("br");
        
        $this->mail->SMPTAuth = true;
        $this->mail->SMTPSecure = "tls";
        $this->mail->CharSet = "utf-8";
        
        $this->mail->Host = MAIL["host"];
        $this->mail->Port = MAIL["port"];
        $this->mail->Username = MAIL["user"];
        $this->mail->Password = MAIL["password"];
    }
    
    public function add(string $subject, string $body, string $recipientName, string $recipientEmail) : EmailAutent 
    {
        $this->data->subject = $subject;
        $this->data->body = $body;
        $this->data->recipient_email = $recipientEmail;
        $this->data->recipient_name = $recipientName;
        return $this;
    }
    
    public function anexar(string $filePath, string $fileName) : EmailAutent {
      $this->data->anexar[$filePath] = $fileName;        
    }
    
    public function send(string $fromName = MAIL["fromName"], string $fromEmail = MAIL["fromEmail"]) : bool {
        try{
            $this->mail->Subject = $this->data->subject;
            $this->mail->msgHTML($this->data->body);
            $this->mail->addAddress($this->data->recipient_email, $this->data->recipient_name);
            $this->mail->setFrom($fromEmail, $fromName);
            
            if(!empty($data->anexar)) {
                foreach($this->data->anexar as $path => $name) {
                    $this->mail->addAttachment($path, $name);
                }
            }
            
            $this->mail->send();
            return true;
        } catch (Exception $exception) {
            $this->error = $exception;
            return false;
        }
    }
    
    public function error() : ?Exception { //PODE RETORNAR NULO(?) OU EXCEPTION
        return $this->error;
    }
        
    
}
