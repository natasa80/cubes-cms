<?php

class Admin_SessionController extends Zend_Controller_Action {

    public function loginAction() {
        Zend_Layout::getMvcInstance()->disableLayout();



        $loginForm = new Application_Form_Admin_Login();


        $request = $this->getRequest();

        //ovde je smoesteno sve sto dobije iz forme§
        //da bi smo imali autocomplete za request
        $request instanceof Zend_Controller_Request_Http;

        
        $flashMessenger = $this->getHelper('FlashMessenger');
        
        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' =>  $flashMessenger->getMessages('errors'),
        );

        if ($request->isPost() && $request->getPost('task') === 'login') {

            if ($loginForm->isValid($request->getPost())) {

                $authAdapter = new Zend_Auth_Adapter_DbTable(); //adapter
                $authAdapter->setTableName('cms_users')
                        ->setIdentityColumn('username')
                        ->setCredentialColumn('password')
                        ->setCredentialTreatment('MD5(?) AND status != 0');

                //sa getValue dobjamo vrednost sa forme, koja je vec trimovana
                $authAdapter->setIdentity($loginForm->getValue('username'));
                $authAdapter->setCredential($loginForm->getValue('password'));
                //
                $auth = Zend_Auth::getInstance();
                //$auth->authenticate($adapter);
                //radi autentifikaciju preko adaptera
                $result = $auth->authenticate($authAdapter);


                if ($result->isValid()) {
                    
                    //u sesiju da ubacimo sve podatke iz tabele pored username i ppass
                    //smestanje kompletnog reda iz tabele cms_users kao identifikator
                    //da je korisnik ulogovan
                    //PO default-u se smesta samo username, a ovako smestamo asocijativni niz, tj row iz tabele
                    
                    //Asocijativni niz user ima kljuceve koji su nazivi kolona u tabeli cms_users
                    $user = $authAdapter->getResultRowObject();
                   
                    $auth->getStorage()->write($user);
                    

                    $redirector = $this->getHelper('Redirector');

                    $redirector instanceof Zend_Controller_Action_Helper_Redirector;


                    $redirector->setExit(true)
                            ->gotoRoute(array(
                                'controller' => 'admin_dashboard',
                                'action' => 'index'
                                    ), 'default', true);
                } else {
                    $systemMessages['errors'][] = 'Wrong username or password';
                }
            } else {
                $systemMessages['errors'][] = 'Username and password are required';
            }
        }

        $this->view->systemMessages =  $systemMessages;
    }

    
    
    
    
    
    
    public function logoutAction() {

        $auth = Zend_Auth::getInstance();


        //brise indikator da je neko ulogovan
        $auth->clearIdentity();
        
        
        $flashMessenger = $this->getHelper('FlashMessenger');
        
        $flashMessenger->addMessage('You have been logged out', 'success');

        //ovde ide redirect na login stranu
        //ovako dobijamo abstraktni helper 
        $redirector = $this->getHelper('Redirector');


        //ovo koristino da bi smo posle imali hintove za redirect
        $redirector instanceof Zend_Controller_Action_Helper_Redirector;



        //prvi parametar gde rutiramo, drugi parametar: koje rutiranje, treci parametar: true-resetuj mi zapamcene vrednosti
        $redirector->setExit(true)
                ->gotoRoute(array(
                    'controller' => 'admin_session', // idi na ovaj kontroler i na akciju login
                    'action' => 'login'
                        ), 'default', true);




        //redirect ako nemamo dodatne parametre
        //$redirector->setExit(true)//ovo je umesto die() fje posle header
        // ->gotoSimple('login', 'admin_session');//ne podrzava parametre
        //JOS JEDNA OPCIJA!!(uglavnom se koristi za spoljni link)
        //$redirector->setExit(true)
        //       ->gotoUrl('/admin_session/login'); 
        //JOS JEDNA OPCIJA!!(uglavnom se koristi za spoljni link)
        //$redirector->setExit(true)
        //          ->setPrependBase(false)
        //       ->gotoUrl('https://www.facebook.com'); 
    }
    
    
    
    
    
    
    
    
    public function indexAction() {

        //proveravamo da li je vec ulogovan

        if (Zend_Auth::getInstance()->hasIdentity()) {
            //ulogovan je
            $redirector = $this->getHelper('Redirector');


            $redirector instanceof Zend_Controller_Action_Helper_Redirector;


            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_dashboard', // idi na ovaj kontroler i na akciju login
                        'action' => 'index'
                            ), 'default', true);
            //redirect na admin_dashboard kontroler i index akciju
        } else {
            //nije ulogovan
            $redirector = $this->getHelper('Redirector');


            $redirector instanceof Zend_Controller_Action_Helper_Redirector;


            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_session', // idi na ovaj kontroler i na akciju login
                        'action' => 'login'
                            ), 'default', true);
        }
        
       
    }

}
