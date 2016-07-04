<?php

class Application_Form_Admin_Login extends Zend_Form
{
    public function init() {
        //kreiramo element 
        $username = new Zend_Form_Element_Text('username');
        //filtriranje skracena verzija od:
        //$username->addFilter(new Zend_Filter-String);
        $username->addFilter('StringTrim')
                ->addFilter('StringToLower')
                ->setRequired(true);//naznacuje se da je element obevezan
                
        //dodavanje u formu
        $this->addElement($username);
        
        
        $password = new Zend_Form_Element_Password('password');
        
        $password->setRequired(true);
        $this->addElement($password);                                                                                                                                                                                                                                                                                                                                                                                                                                                         
        
        
        
        
    }

}