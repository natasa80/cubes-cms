<?php

class AboutusController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function indexAction() {
        $request = $this->getRequest();
        $flashMessenger = $this->getHelper('FlashMessenger');

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors'),
        );


        //prikaz svih membera
        $cmsMembersDbTable = new Application_Model_DbTable_CmsMembers();


        //select je objekat klase Zend_Db_ Select
        $select = $cmsMembersDbTable->select();
        $select->where('status = ?', Application_Model_DbTable_CmsMembers::STATUS_ENABLED)
                ->order('order_number');


       

        $sitemapPageId = (int) $request->getParam('sitemap_page_id');
//          print_r($sitemapPageId);
//        die();
        $cmsSitemapPageDbTable = new Application_Model_DbTable_CmsSitemapPages();
        $sitemapPage = $cmsSitemapPageDbTable->getSitemapPageById($sitemapPageId);
        
        if ($sitemapPageId <= 0) {
            throw new Zend_Controller_Router_Exception('Invalid sitemap  is found with id ' . $sitemapPageId, 404);
        }

        if (!$sitemapPage) {

            throw new Zend_Controller_Router_Exception('Invalid sitemap  is found with id ' . $sitemapPageId, 404);
        }




        if (
                $sitemapPage['status'] == Application_Model_DbTable_CmsSitemapPages::STATUS_DISABLED
                //check if user is not logged in, than preview is not available
                //for disabled pages
                && !Zend_Auth::getInstance()->hasIdentity()
        ) {
            throw new Zend_Controller_Router_Exception('No sitemap page is disabled ', 404);
        }


        //debug za db select - vraca se sql upit
        //die($select->assemble());


        $members = $cmsMembersDbTable->fetchAll($select);
        $this->view->sitemapPage = $sitemapPage;
        $this->view->members = $members;
        $this->view->systemMessages = $systemMessages;
    }

    public function memberAction() {
        $request = $this->getRequest();
        //filtriranje

        $id = $request->getParam('id');
        $id = trim($id);
        $id = (int) $id;


        //vlidacija
        if (empty($id)) {

            throw new Zend_Controller_Router_Exception('No member id', 404);
        }



        $cmsMembersDbTable = new Application_Model_DbTable_CmsMembers();


        $select = $cmsMembersDbTable->select();
        $select->where('id =?', $id)
                ->where('status = ?', Application_Model_DbTable_CmsMembers::STATUS_ENABLED);

        $foundMembers = $cmsMembersDbTable->fetchAll($select);


        if (count($foundMembers) <= 0) {

            throw new Zend_Controller_Router_Exception('No member is found for member: ' . $id, 404);
        }


        $member = $foundMembers[0];

//        $memberSlug = $request->getParam('member_slug');
//        if(empty($memberSlug)){
//             $redirector = $this->getHelper('Redirector');
//                        $redirector->setExit(true)
//                                ->gotoRoute(array( 
//                                    'id' => $member['id'],
//                                    'member_slug' => $member['first_name'] . '-' . $member['last_name']
//                                        ), 'member-route', true);
//        }
        //Fetching all other member
        $select = $cmsMembersDbTable->select();
        $select->where('status = ?', Application_Model_DbTable_CmsMembers::STATUS_ENABLED)
                ->where('id != ?', $id)
                ->order('order_number');

        $members = $cmsMembersDbTable->fetchAll($select);
        $this->view->members = $members;


        $this->view->member = $member;
    }

    public function aboutusAction() {

        $flashMessenger = $this->getHelper('FlashMessenger');

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors'),
        );


        //prikaz svih membera
        $cmsMembersDbTable = new Application_Model_DbTable_CmsMembers();


        //select je objekat klase Zend_Db_ Select
        $select = $cmsMembersDbTable->select();
        $select->where('status = ?', Application_Model_DbTable_CmsMembers::STATUS_ENABLED)
                ->order('order_number');


        $request = $this->getRequest();




        $members = $cmsMembersDbTable->fetchAll($select);
        $this->view->members = $members;
        $this->view->systemMessages = $systemMessages;
    }

}
