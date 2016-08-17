<?php

class Application_Form_Admin_PhotoGalleryAdd extends Zend_Form

{   //override jer vec postoji u zend formi
    public function init() {
        
        $title= new Zend_Form_Element_Text('title');//atribut je isti kao sa forme
        //$firstName->addFilter(new Zend_Filter_StringTrim);
        //$firstName->addValidator(new Zend_Validate_StringLength(array('min' => 3, 'max' => 255)));
        
        $title->addFilter('StringTrim')
                ->addValidator('StringLength', false, array('min' => 3, 'max' => 255))//false znaci da ako posle ovog ima jos validatora da ne prekida ispitivanje i validaciju i ako ne prodje ovu
                ->setRequired(true);//ispituje da li je prazan string
        
        //treba sada da se ubaci u formu
        $this->addElement($title);
        
         
        
        $description = new Zend_Form_Element_Textarea('description');
        $description->addFilter('StringTrim')
                ->setRequired(false);
        $this->addElement($description);
        
        //na nivou elementa, ako imamo true parametar, i oko izbaci gresku za tu validaciju i ne ispituje dalje 
        $photoGallerieLeadingPhoto = new Zend_Form_Element_File('photo_gallery_leading_photo');
        $photoGallerieLeadingPhoto->addValidator('Count', true, 1)//ogranicavamo broj fajlova koji se mogu uploud-ovati 
                    ->addValidator('MimeType', true, array('image/jpeg', 'image/gif', 'image/png'))
                    ->addValidator('ImageSize', false, array(
                        'minwidth' => 360,
                        'minheight' => 270,
                        'maxwidth' => 2000,
                        'maxheight' => 2000
                    ))
                    ->addValidator('Size', false, array(
                        'max' => '10MB'
                    ))
                    // disable move file to destination when calling method getValues
                    ->setValueDisabled(true)
                    ->setRequired(true);
        
            $this->addElement($photoGallerieLeadingPhoto);
                
    }

}