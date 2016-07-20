<?php

class Application_Plugin_Admin extends Zend_Controller_Plugin_Abstract {

//izvrssava se svaki put kada se  zavrsi rutiranje
//pre dispatchinga se izvrsava
//vec je spreman request objekat
    public function routeShutdown(Zend_Controller_Request_Abstract $request) {

    //dobijamo controller koji je pozvan i akciju koja je pozvana
        $controllerName = $request->getControllerName();

        $actionName = $request->getActionName();

        //ispitujemo da li controller name pocinje sa admin, onda znamo da je admin controller

        if (preg_match('/^admin_/', $controllerName)) {

            Zend_Layout::getMvcInstance()->setLayout('admin');

                //proveravamo da li je ulogovan
            if (
                    !Zend_Auth::getInstance()->hasIdentity() && $controllerName != 'admin_session'
            ) {
                   $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
                   $flashMessenger->addMessage('You must login', 'errors');
                //dobijamo redirector vam kontrolera
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');

                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_session', // idi na ovaj kontroler i na akciju login
                            'action' => 'login'
                                ), 'default', true);
            }
        }
    }

}
