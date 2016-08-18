<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initRouter() {
        $this->bootstrap('db'); //da bi se pre bootstrapinga ucitala baza ostvarila konekcija
        
        
        //OVDE PUNIMO TIPOVE STRANICA!!!!!
        $sitemapPageTypes = array(
            //ovaj deo cemo uvek imati u cms-u
            'StaticPage' => array(
                'title' => 'Static Page',
                'subtypes' => array(
                    //tip stranice koji se moze naci pod ovom 
                    'StaticPage' => 0  //0 means ulimited number
                )
            ),
            'AboutUsPage' => array(
                'title' => 'About Us Page',
                'subtypes' => array(
                //nema podtipova
                )
            ),
            'ServicesPage' => array(
                'title' => 'Services Page',
                'subtypes' => array(
                )
            ),
            'ContactPage' => array(
                'title' => 'Contact Page',
                'subtypes' => array(
                )
            ),
            'PhotoGalleriesPage' => array(
                'title' => 'PhotoGalleries Page',
                'subtypes' => array(
                )
            ),
        );
        //definisemo tipove stranoica koje mogu doci u root//parent id =0
        $rootSitemapPageTypes = array(
            'StaticPage' => 0,
            'AboutUsPage' => 1,
            'ServicesPage' => 1,
            'ContactPage' => 1,
            'PhotoGalleriesPage' => 1
        );
        //preporucljivo je da se registar puni u bootsrapu
        //klasa koja implementira singleton
        Zend_Registry::set('sitemapPageTypes', $sitemapPageTypes);
        Zend_Registry::set('rootSitemapPageTypes', $rootSitemapPageTypes);

        $router = Zend_Controller_Front::getInstance()->getRouter();

        $router instanceof Zend_Controller_Router_Rewrite;


        $router->addRoute('contact-us-route', new Zend_Controller_Router_Route_Static(
                'contact-us', array(
            'controller' => 'contact',
            'action' => 'index'
                )
        ))->addRoute('ask-member-route', new Zend_Controller_Router_Route(
                'ask-member/:id/:member_slug', array(
            'controller' => 'contact',
            'action' => 'askmember',
            'member_slug' => ''
        )));


        $sitemapPagesMap = Application_Model_DbTable_CmsSitemapPages::getSitemapPagesMap();

        // print_r($sitemapPagesMap); die();
        foreach ($sitemapPagesMap as $sitemapPageId => $sitemapPageMap) {

            if ($sitemapPageMap['type'] == 'StaticPage') {

                $router->addRoute('static-page-route-' . $sitemapPageId, new Zend_Controller_Router_Route_Static(
                        $sitemapPageMap['url'], array(
                    'controller' => 'staticpage',
                    'action' => 'index',
                    'sitemap_page_id' => $sitemapPageId
                        )
                ));
            }
            if ($sitemapPageMap['type'] == 'AboutUsPage') {
//                print_r($sitemapPageId);
//                die();
                $router->addRoute('static-page-route-' . $sitemapPageId, new Zend_Controller_Router_Route_Static(
                        $sitemapPageMap['url'], array(
                    'controller' => 'aboutus',
                    'action' => 'index',
                    'sitemap_page_id' => $sitemapPageId
                        )
                ));

                $router->addRoute('member-route', new Zend_Controller_Router_Route(
                        $sitemapPageMap['url'] . '/member/:id/:member_slug', array(
                    'controller' => 'aboutus',
                    'action' => 'member',
                    'member_slug' => ''
                        )
                ));
            }
            
            if ($sitemapPageMap['type'] == 'ContactPage') {
//                       print_r($sitemapPageId);
//               die();
                $router->addRoute('static-page-route-' . $sitemapPageId, new Zend_Controller_Router_Route_Static(
                        $sitemapPageMap['url'], array(
                    'controller' => 'contact',
                    'action' => 'index',
                    'sitemap_page_id' => $sitemapPageId
                        )
                ));
            }
            if ($sitemapPageMap['type'] == 'ServicesPage') {
                
                $router->addRoute('static-page-route-' . $sitemapPageId, new Zend_Controller_Router_Route_Static(
                        $sitemapPageMap['url'], array(
                    'controller' => 'services',
                    'action' => 'index',
                    'sitemap_page_id' => $sitemapPageId
                        )
                ));
            }
            if ($sitemapPageMap['type'] == 'PhotoGalleriesPage') {
                
                $router->addRoute('static-page-route-' . $sitemapPageId, new Zend_Controller_Router_Route_Static(
                        $sitemapPageMap['url'], array(
                    'controller' => 'photogalleries',
                    'action' => 'index',
                    'sitemap_page_id' => $sitemapPageId
                        )
                ));
                
                $router->addRoute('photo-gallery-route', new Zend_Controller_Router_Route(
                        $sitemapPageMap['url'] . '/:id/:photo_gallery_slug', array(
                    'controller' => 'photogalleries',
                    'action' => 'gallery',
                    'sitemap_page_id' => $sitemapPageId
                        )
                ));
            }
        }
    }

}
