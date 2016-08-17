<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
         $flashMessenger = $this->getHelper('FlashMessenger');
        
        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' =>  $flashMessenger->getMessages('errors'),
        );
        
       
        
        //prikaz  slide-ova
        //
        $cmsSlidesDBTable = new Application_Model_DbTable_CmsIndexSlides ();
       
       $indexSlides = $cmsSlidesDBTable->search(array(
           'filters' => array(
               'status' => Application_Model_DbTable_CmsIndexSlides::STATUS_ENABLED
           ),
           'orders' => array(
                'order_number' => 'ASC',
            )
       ));
       
        
        //prikaz servisa
        $cmsServicesDbTable = new Application_Model_DbTable_CmsServices();
       
       
        $services = $cmsServicesDbTable->search(array(
           'filters' => array(
               'status' => Application_Model_DbTable_CmsServices::STATUS_ENABLED
           ),
           'orders' => array(
                'order_number' => 'ASC',
            ),
            'limit'=> 4
       ));
        
        //sitemappage
        
        $cmsSitemapPagesDbTable = new Application_Model_DbTable_CmsSitemapPages();
        $servicesSitemapPages = $cmsSitemapPagesDbTable->search(array(
			'filters' => array(
				'status' => Application_Model_DbTable_CmsSitemapPages::STATUS_ENABLED,
				'type' => 'ServicesPage'
			)
		));
        
        $serviceSitemapPageId = $servicesSitemapPages[0]['id'];
            

        $this->view->serviceSitemapPageId = $serviceSitemapPageId;
        $this->view->services = $services;
        $this->view->indexSlides = $indexSlides;
        $this->view->systemMessages =  $systemMessages;
    }
    
    //
    
    
    
    public function testAction()
    {
        
        
    }


}

