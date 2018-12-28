<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GestioMails
 *
 * @author xhuix
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/phpmailer/src/Exception.php';
require '../phpmailer/phpmailer/src/PHPMailer.php';
require '../phpmailer/phpmailer/src/SMTP.php';

class GestioMailsNadal {

    private $mail;

    //constructor
    public function __construct($compteSMTP, $passwordsSMPT, $SMTP) {


        //s'instancia la classe
        $this->mail = new PHPMailer();
        //Server settings
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Username = $compteSMTP;
        $this->mail->Password = $passwordsSMPT;
        $this->mail->CharSet = "utf-8";
        $this->mail->FromName = "INS ROCAGROSSA   ";
        $this->mail->Subject = "INFORMACIÓ INS ROCAGROSSA";
        $this->mail->isHTML(true);

        $this->mail->AltBody = 'per altres clients';
        //$this->mail->Host = "smtp.live.com";
        $this->mail->Host = $SMTP;
        $this->mail->Port = 587;
        $this->mail->IsSMTP();
        $this->mail->SMTPAuth = true;
        $this->mail->From = $compteSMTP;
        $this->mail->AddEmbeddedImage('../imatges/postal_nadal_2017_2018.jpg', 'nadal_roca');


        //$this->mail->Port = 25;
        //$this->mail->SMTPAuth = false;
        //$this->mail->SMTPSecure = false;
    }

    public function afegeixAdreca($adreca) {

        $this->mail->AddAddress($adreca);
    }

    public function afegeixBcc($adreca) {

        $this->mail->addBCC($adreca);
    }

    public function enviaMail() {
        try {
            $this->mail->Send();

            return 0;
        } catch (PHPMailer\PHPMailer\Exception $ex) {
            return 1;
        }
    }

    public function afegeixCosHtml($cosHtml) {
        $this->mail->Body = $cosHtml;
    }

    public function netejaAdreces() {

        $this->mail->clearAllRecipients();
    }

}
