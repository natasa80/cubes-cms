<?php

class Application_Form_Admin_IndexSlideAdd extends Zend_Form

{   //override jer vec postoji u zend formi
    public function init() {
        
        $title = new Zend_Form_Element_Text('title');//atribut je isti kao sa forme
        //$title->addFilter(new Zend_Filter_StringTrim);
        //$title->addValidator(new Zend_Validate_StringLength(array('min' => 3, 'max' => 255)));
        
        $title->addFilter('StringTrim')
                ->addValidator('StringLength', false, array('min' => 3, 'max' => 255))//false znaci da ako posle ovog ima jos validatora da ne prekida ispitivanje i validaciju i ako ne prodje ovu
                ->setRequired(true);//ispituje da li je prazan string
        
        //treba sada da se ubaci u formu
        $this->addElement($title);
        
        $linkType = new Zend_Form_Element_Select('link_type');
        $linkType->addMultiOption('NoLink', 'No Link is displayed in slide')
                ->addMultiOption('SitemapPage', 'Link to sitemap page')
                ->addMultiOption('InternalLink', 'Link to internal url')
                ->addMultiOption('ExternalLink', 'Link to external site')
                ->setRequired(true);
        $this->addElement($linkType);
                
                
        $linkLabel = new Zend_Form_Element_Text('link_label');
        $linkLabel->setRequired(false);
        $this->addElement($linkLabel);
        
        
        $sitemapPageId = new Zend_Form_Element_Text('sitemap_page_id');
        $sitemapPageId->setRequired(false);
        $this->addElement($sitemapPageId);
        
        $internalLinkUrl = new Zend_Form_Element_Text('internal_link_url');
        $internalLinkUrl->setRequired(false);
        $this->addElement($internalLinkUrl);
        
        $externalLinkUrl = new Zend_Form_Element_Text('external_link_url');
        $externalLinkUrl->setRequired(false);
        $this->addElement($externalLinkUrl);
        
        
        
        
        
        $description = new Zend_Form_Element_Textarea('description');
        $description->addFilter('StringTrim')
                ->setRequired(false);
        $this->addElement($description);
        
        //na nivou elementa, ako imamo true parametar, i oko izbaci gresku za tu validaciju i ne ispituje dalje 
        $indexSlidePhoto = new Zend_Form_Element_File('index_slide_photo');
        $indexSlidePhoto->addValidator('Count', true, 1)//ogranicavamo broj fajlova koji se mogu uploud-ovati 
                    ->addValidator('MimeType', true, array('image/jpeg', 'image/gif', 'image/png'))
                    ->addValidator('ImageSize', false, array(
                        'minwidth' => 600,
                        'minheight' => 400,
                        'maxwidth' => 2000,
                        'maxheight' => 2000
                    ))
                    ->addValidator('Size', false, array(
                        'max' => '10MB'
                    ))
                    // disable move file to destination when calling method getValues
                    ->setValueDisabled(true)
                    ->setRequired(false);
        
            $this->addElement($indexSlidePhoto);
                
    }

}