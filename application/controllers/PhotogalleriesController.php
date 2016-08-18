<?php

class PhotogalleriesController extends Zend_Controller_Action {

    public function galleryAction() {

        $request = $this->getRequest();

        $id = (int) $request->getParam('id');

        if ($id <= 0) {
            //prekida se izvrsavanje proograma i prikazuje se page not found
            throw new Zend_Controller_Router_Exception('Invalid photoGallery id: ' . $id, 404);
        }


        $cmsPhotoGalleriesTable = new Application_Model_DbTable_CmsPhotoGalleries();

        $photoGallery = $cmsPhotoGalleriesTable->getPhotoGalleryById($id);

        if (empty($photoGallery)) {
            //prekida se izvrsavanje proograma i prikazuje se page not found
            throw new Zend_Controller_Router_Exception('No photoGallery is found with id ' . $id, 404);
        }



        $flashMessenger = $this->getHelper('FlashMessenger');

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors'),
        );


        $cmsPhotosDbTable = new Application_Model_DbTable_CmsPhotos();
        $photos = $cmsPhotosDbTable->search(array(
            'filters' => array(
                'photo_gallery_id' => $photoGallery['id']
            ),
            'orders' => array(
                'order_number' => 'ASC'
            ),
        ));
        
        $sitemapPageId = (int) $request->getParam('sitemap_page_id');
         
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


        $this->view->sitemapPage = $sitemapPage;
        $this->view->photos = $photos;
        $this->view->photoGallery = $photoGallery;
        $this->view->systemMessages = $systemMessages;
    }

    public function indexAction() {

        $cmsPhotoGalleriesTable = new Application_Model_DbTable_CmsPhotoGalleries ();

        $photoGalleries = $cmsPhotoGalleriesTable->search(array(
            'filters' => array(
                'status' => Application_Model_DbTable_CmsPhotoGalleries::STATUS_ENABLED
            ),
            'orders' => array(
                'order_number' => 'ASC',
            ),
            'limit' => 3
        ));
        
        $request = $this->getRequest();

        $id = (int) $request->getParam('id');
        $sitemapPageId = (int) $request->getParam('sitemap_page_id');
         
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


        $this->view->sitemapPage = $sitemapPage;

        $this->view->photoGalleries = $photoGalleries;
    }

}
