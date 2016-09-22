<?php


class Application_Model_Library_MailHelper
{
    public function sendmail($to_email, $from_email, $from_name, $message){
        
        
        $mail = new Zend_Mail('UTF-8');
        $mail->setSubject('Poruka sa kontakt forme | Cubes CMS');
        $mail->addTo($to_email);
        $mail->setFrom($from_email, $from_name);
        $mail->setBodyHtml($message);
        $mail->setBodyText($message);  //alternativa ukoliko ne postoji podrska za html
        
        return $result = $mail->send();
        
        
    }
    
}