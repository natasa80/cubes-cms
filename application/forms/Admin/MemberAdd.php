<?php

class Application_Form_Admin_MemberAdd extends Zend_Form

{   //override jer vec postoji u zend formi
    public function init() {
        
        $firstName = new Zend_Form_Element_Text('first_name');//atribut je isti kao sa forme
        //$firstName->addFilter(new Zend_Filter_StringTrim);
        //$firstName->addValidator(new Zend_Validate_StringLength(array('min' => 3, 'max' => 255)));
        
        $firstName->addFilter('StringTrim')
                ->addValidator('StringLength', false, array('min' => 3, 'max' => 255))//false znaci da ako posle ovog ima jos validatora da ne prekida ispitivanje i validaciju i ako ne prodje ovu
                ->setRequired(true);//ispituje da li je prazan string
        
        //treba sada da se ubaci u formu
        $this->addElement($firstName);
        
          $lastName = new Zend_Form_Element_Text('last_name');//atribut je isti kao sa forme
        
        
        $lastName->addFilter('StringTrim')
                ->addValidator('StringLength', false, array('min' => 3, 'max' => 255))
                ->setRequired(true);
        $this->addElement($lastName);
        
        
        $workTitle = new Zend_Form_Element_Text('work_title');
        $workTitle->addFilter('StringTrim')
                ->addValidator('StringLength', false, array('min' => 3, 'max' => 255))
                ->setRequired(false);
        $this->addElement($workTitle);
        
        $email = new Zend_Form_Element_Text('email');
        $email->addFilter('StringTrim')
                ->addValidator('EmailAddress', false, array('domain'=> false))//validira da li postoji domenski deo na netu, ta opcija treba da se iskljuci
                ->setRequired(true);
        $this->addElement($email);
        
        $resume = new Zend_Form_Element_Textarea('resume');
        $resume->addFilter('StringTrim')
                ->setRequired(false);
        $this->addElement($resume);
        
        //na nivou elementa, ako imamo true parametar, i oko izbaci gresku za tu validaciju i ne ispituje dalje 
        $memberPhoto = new Zend_Form_Element_File('member_photo');
        $memberPhoto->addValidator('Count', true, 1)//ogranicavamo broj fajlova koji se mogu uploud-ovati 
                    ->addValidator('MimeType', true, array('image/jpeg', 'image/gif', 'image/png'))
                    ->addValidator('ImageSize', false, array(
                        'minwidth' => 150,
                        'minheight' => 150,
                        'maxwidth' => 2000,
                        'maxheight' => 2000
                    ))
                    ->addValidator('Size', false, array(
                        'max' => '10MB'
                    ))
                    // disable move file to destination when calling method getValues
                    ->setValueDisabled(true)
                    ->setRequired(false);
        
            $this->addElement($memberPhoto);
                
    }

}