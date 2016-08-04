<?php

class Admin_SitemapController extends Zend_Controller_Action 
{
    public function indexAction(){
         $request = $this->getRequest();
        
        $flashMessenger = $this->getHelper('FlashMessenger');
        
     

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors')
        );
        //if no request_id parametere, than $Id will be 0
        $id = (int) $request->getParam('id', 0);
        
        if ($id < 0){
            throw new  Zend_Controller_Router_Exception('Invalid parent id for site map pages ', 404);
        }
        
       
        $cmsSitemapPagesDbTable = new Application_Model_DbTable_CmsSitemapPages();
        
        $sitemapPage = $cmsSitemapPagesDbTable->getSitemapPageById($id);
        
        if (!$sitemapPage && $id !=0){
            throw new  Zend_Controller_Router_Exception('No sitemap is found for sitemap pages ', 404);
        }
        
        
        $childSitemapPages = $cmsSitemapPagesDbTable->search(array(
            'filters' => array(
                'parent_id' => $id
            ),
            'orders' => array(
                'order_number' > 'ASC'
            ),
            //'limit' => 50,
            //'page' => 3
        ));
        
        $sitemapPageBreadcrumbs = $cmsSitemapPagesDbTable->getSitemapPageBreadcrumbs($id);
        
        
        $this->view->childSitemapPages = $childSitemapPages;
        
        $this->view->systemMessages = $systemMessages;
        $this->view->sitemapPageBreadcrumbs = $sitemapPageBreadcrumbs;
    }
}