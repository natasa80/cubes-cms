<?php

class ServicesController extends Zend_Controller_Action
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
        
        
        //prikaz svih servisa
        $cmsServicesDbTable = new Application_Model_DbTable_CmsServices();
       
        $select = $cmsServicesDbTable->select();
        $select->where('status = ?', Application_Model_DbTable_CmsServices::STATUS_ENABLED)
                ->order('order_number');
                
       
        $services = $cmsServicesDbTable->fetchAll($select);
        $this->view->services = $services;
        $this->view->systemMessages =  $systemMessages;
        
    }
    
    public function serviceAction(){
        
    }

}

