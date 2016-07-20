<?php

class Application_Form_Admin_ServiceAdd extends Zend_Form

{   //override jer vec postoji u zend formi
    public function init() {
        
        $title = new Zend_Form_Element_Text('title');//atribut je isti kao sa forme
        //$firstName->addFilter(new Zend_Filter_StringTrim);
        //$firstName->addValidator(new Zend_Validate_StringLength(array('min' => 3, 'max' => 255)));
        
        $title->addFilter('StringTrim')
                ->addValidator('StringLength', false, array('min' => 3, 'max' => 255))//false znaci da ako posle ovog ima jos validatora da ne prekida ispitivanje i validaciju i ako ne prodje ovu
                ->setRequired(true);//ispituje da li je prazan string
        
        //treba sada da se ubaci u formu
        $this->addElement($title);
        
          $icon = new Zend_Form_Element_Text('icon');//atribut je isti kao sa forme
        
        
        $icon->addFilter('StringTrim')
                ->addValidator('StringLength', false, array('min' => 3, 'max' => 255))
                ->setRequired(true);
        $this->addElement($icon);
        
       
        
        $description = new Zend_Form_Element_Textarea('description');
        $description->addFilter('StringTrim')
                ->setRequired(true);
        $this->addElement($description);
                
    }

}