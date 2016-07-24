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
        
        
        //prikaz svih servisa
        $cmsClientsDbTable = new Application_Model_DbTable_CmsClients();
       
        $select = $cmsClientsDbTable->select();
        $select->where('status = ?', Application_Model_DbTable_CmsClients::STATUS_ENABLED)
                ->order('order_number', 'DESC');
                
       
        $clients = $cmsClientsDbTable->fetchAll($select);
        $this->view->clients = $clients;
        $this->view->systemMessages =  $systemMessages;
    }
    
    //
    
    
    
    public function testAction()
    {
        
        
    }


}

