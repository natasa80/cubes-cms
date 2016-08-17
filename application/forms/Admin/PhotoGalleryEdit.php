<?php

class Application_Form_Admin_PhotoGalleryEdit extends Application_Form_Admin_PhotoGalleryAdd

{   //override jer vec postoji u zend formi
    
    public function init() {
        parent::init();
        $this->getElement('photo_gallery_leading_photo')->setRequired(false);
    }

}