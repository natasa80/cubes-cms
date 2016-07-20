<?php

class Application_Form_Admin_UserAdd extends Zend_Form

{   //override jer vec postoji u zend formi
    public function init() {
        
        $username = new  Zend_Form_Element_Text('username');
        $username->addFilter('StringTrim')
                ->addValidator('StringLength', false, array('min' => 3, 'max' => 50))
                ->addValidator(new Zend_Validate_Db_NoRecordExists(array(
                 'table' => 'cms_users',
                 'field' => 'username'
                    )))
                ->setRequired(true);
        $this->addElement($username);
        
       
        
        $firstName = new Zend_Form_Element_Text('first_name');//atribut je isti kao sa forme
        //$firstName->addFilter(new Zend_Filter_StringTrim);
        //$firstName->addValidator(new Zend_Validate_StringLength(array('min' => 3, 'max' => 255)));
        
        $firstName->addFilter('StringTrim')
                ->addValidator('StringLength', false, array('min' => 3, 'max' => 255))//false znaci da ako posle ovog ima jos validatora da ne prekida ispitivanje i validaciju i ako ne prodje ovu
                ->setRequired(false);//ispituje da li je prazan string
        
        //treba sada da se ubaci u formu
        $this->addElement($firstName);
        
          $lastName = new Zend_Form_Element_Text('last_name');//atribut je isti kao sa forme
        
        
        $lastName->addFilter('StringTrim')
                ->addValidator('StringLength', false, array('min' => 3, 'max' => 255))
                ->setRequired(false);
        $this->addElement($lastName);
        
        
        
        $email = new Zend_Form_Element_Text('email');
        $email->addFilter('StringTrim')
                ->addValidator('EmailAddress', false, array('domain'=> false))//validira da li postoji domenski deo na netu, ta opcija treba da se iskljuci
                ->setRequired(false);
        $this->addElement($email);
        
                
    }

}