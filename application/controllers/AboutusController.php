<?php

class AboutusController extends Zend_Controller_Action
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
        
        
        //prikaz svih membera
        $cmsMembersDbTable = new Application_Model_DbTable_CmsMembers();
        
        //select je objekat klase Zend_Db_ Select
        $select = $cmsMembersDbTable->select();
        $select->where('status = ?', Application_Model_DbTable_CmsMembers::STATUS_ENABLED)
                ->order('order_number');
        
        
        //debug za db select - vraca se sql upit
         //die($select->assemble());
        
        
        $members = $cmsMembersDbTable->fetchAll($select);
        $this->view->members = $members;
        $this->view->systemMessages =  $systemMessages;
        
    }

    public function memberAction()
    {
        $request = $this->getRequest();
        //filtriranje
        
        $id = $request->getParam('id');
        $id = trim($id);
        $id = (int)$id;
        
        
        //vlidacija
        if (empty($id)){
            
            throw  new Zend_Controller_Router_Exception('No member id', 404);
        
        }
        
        $cmsMembersDbTable = new Application_Model_DbTable_CmsMembers();
        
        
        $select = $cmsMembersDbTable->select();
        $select->where('id =?',  $id)
                ->where('status = ?', Application_Model_DbTable_CmsMembers::STATUS_ENABLED);
        
        $foundMembers = $cmsMembersDbTable->fetchAll($select);
                
        
         if (count($foundMembers) <= 0){
            
            throw  new Zend_Controller_Router_Exception('No member is found for member: ' . $id,  404);
        
        }
        
        
        $member = $foundMembers[0];
        //Fetching all other member
         $select = $cmsMembersDbTable->select();
        $select->where('status = ?', Application_Model_DbTable_CmsMembers::STATUS_ENABLED)
                ->where('id != ?', $id)
                ->order('order_number');
        
        $members = $cmsMembersDbTable->fetchAll($select);
        $this->view->members = $members;
        
        
        $this->view->member = $member;
    }
}

