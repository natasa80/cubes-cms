<?php

class Admin_UsersController extends Zend_Controller_Action {

    public function indexAction() {

        $flashMessenger = $this->getHelper('FlashMessenger');

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors'),
        );


        $cmsUsersDbTable = new Application_Model_DbTable_CmsUsers();
        
        $loggedInUser = Zend_Auth::getInstance()->getIdentity();
        
        $users = $cmsUsersDbTable->search(array(
            'filters' => array(
                'id_exclude' => $loggedInUser['id']
            ),
            'orders' =>array(
                'first_name' =>'ASC'
            ),
//            'limit' =>3,
//            'page' => 2
        ));

        $this->view->users = $users;
        $this->view->systemMessages = $systemMessages;
    }

    
    //ADD
    public function addAction() {

        $request = $this->getRequest(); //podaci iz url-a iz forme koje dobijemo
        $flashMessenger = $this->getHelper('FlashMessenger');

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors'),
        );

        $form = new Application_Form_Admin_UserAdd();

//default form data//mi nemamo default vrednosti
        $form->populate(array(
        ));

        if ($request->isPost() && $request->getPost('task') === 'save') {//ispitujemo da lije pokrenuta forma
            try {

                //check form is valid
                if (!$form->isValid($request->getPost())) {//sve sto je u post zahtevu prosledi formi na validaciju
                    throw new Application_Model_Exception_InvalidInput('Invalid data was sent for new user');
                }

                //get form data//vrednosti iz forme se uzimaju preko getVaues i upisuju u niz
                //to su filtrirani i validirani podaci
                $formData = $form->getValues();


                //insertujemo zapis u bazu
                $cmsUsersTable = new Application_Model_DbTable_CmsUsers();

                //insert member returns ID of the new member
                $userId = $cmsUsersTable->insertUser($formData);

                $flashMessenger->addMessage('User has been saved', 'success');

                //redirect to same or another page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_users',
                            'action' => 'index'
                                ), 'default', true);
            } catch (Application_Model_Exception_InvalidInput $ex) {
                $systemMessages['errors'][] = $ex->getMessage();
            }
        }

        $this->view->systemMessages = $systemMessages;
        $this->view->form = $form;
    }

    
    
    //EDIT
    public function editAction() {

        $request = $this->getRequest();

        $id = (int) $request->getParam('id');

        if ($id <= 0) {
            //prekida se izvrsavanje proograma i prikazuje se page not found
            throw new Zend_Controller_Router_Exception('Invalid user id: ' . $id, 404);
        }

        $loggedinUser = Zend_Auth::getInstance()->getIdentity();

        if ($id == $loggedinUser['id']) {
            //redirect user to edit profile page
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_profile',
                        'action' => 'edit'
                            ), 'default', true);
        }

        $cmsUsersTable = new Application_Model_DbTable_CmsUsers();
        $user = $cmsUsersTable->getUserById($id);

        if (empty($user)) {
            //prekida se izvrsavanje proograma i prikazuje se page not found
            throw new Zend_Controller_Router_Exception('No user is found with id ' . $id, 404);
        }


        $flashMessenger = $this->getHelper('FlashMessenger');

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors'),
        );

        $form = new Application_Form_Admin_UserEdit($user['id']);

        //default form data//mi nemamo default vrednosti
        $form->populate($user);



        if ($request->isPost() && $request->getPost('task') === 'update') {//ispitujemo da lije pokrenuta forma
            try {

                //check form is valid
                if (!$form->isValid($request->getPost())) {//sve sto je u post zahtevu prosledi formi na validaciju
                    throw new Application_Model_Exception_InvalidInput('Invalid data was sent for  user');
                }


                $formData = $form->getValues();


                $cmsUsersTable->updateUser($user['id'], $formData);

                // do actual task
                //save to database etc
                //set system message
                $flashMessenger->addMessage('User has been updated', 'success');

                //redirect to same or another page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_users',
                            'action' => 'index'
                                ), 'default', true);
            } catch (Application_Model_Exception_InvalidInput $ex) {
                $systemMessages['errors'][] = $ex->getMessage();
            }
        }

        $this->view->systemMessages = $systemMessages;
        $this->view->form = $form;

        $this->view->user = $user;
    }

    
    //DISABLE
    public function disableAction() {

        $request = $this->getRequest();

        if (!$request->isPost() || $request->getPost('task') != 'disable') {

            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_users',
                        'action' => 'index'
                            ), 'default', true);
        }

        $flashMessenger = $this->getHelper('FlashMessenger');

        try {

            $id = (int) $request->getPost('id');


            if ($id <= 0) {
                throw new Application_Model_Exception_InvalidInput('Invalid user id: ' . $id, 'errors');
            }

            $loggedinUser = Zend_Auth::getInstance()->getIdentity();

            if ($id == $loggedinUser['id']) {
                //redirect user to edit profile page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_profile',
                            'action' => 'edit'
                                ), 'default', true);
            }


            $cmsUsersTable = new Application_Model_DbTable_CmsUsers();
            $user = $cmsUsersTable->getUserById($id);

            if (empty($user)) {

                throw new Application_Model_Exception_InvalidInput('No user is found with id: ' . $id, 'errors');
            }

            $cmsUsersTable->disableUser($id);


            $flashMessenger->addMessage('User: ' . $user['first_name'] . ' ' . $user['last_name'] . 'has been disabled', 'success');

            //redirect on another page
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_users',
                        'action' => 'index'
                            ), 'default', true);
        } catch (Application_Model_Exception_InvalidInput $ex) {

            $flashMessenger->addMessage($ex->getMessage(), 'errors');

            //redirect on another page
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_users',
                        'action' => 'index'
                            ), 'default', true);
        }
    }

    
    //ENABLE
    public function enableAction() {

        $request = $this->getRequest();

        if (!$request->isPost() || $request->getPost('task') != 'enable') {

            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_users',
                        'action' => 'index'
                            ), 'default', true);
        }

        $flashMessenger = $this->getHelper('FlashMessenger');

        try {

            //read $_POST
            $id = (int) $request->getPost('id');


            if ($id <= 0) {
                throw new Application_Model_Exception_InvalidInput('Invalid user id: ' . $id, 'errors');
            }

            $loggedinUser = Zend_Auth::getInstance()->getIdentity();

            if ($id == $loggedinUser['id']) {
                //redirect user to edit profile page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_profile',
                            'action' => 'edit'
                                ), 'default', true);
            }


            $cmsUsersTable = new Application_Model_DbTable_CmsUsers();
            $user = $cmsUsersTable->getUserById($id);

            if (empty($user)) {

                throw new Application_Model_Exception_InvalidInput('No user is found with id: ' . $id, 'errors');
            }

            $cmsUsersTable->enableUser($id);


            $flashMessenger->addMessage('User: ' . $user['first_name'] . ' ' . $user['last_name'] . 'has been enabled', 'success');

            //redirect on another page
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_users',
                        'action' => 'index'
                            ), 'default', true);
        } catch (Application_Model_Exception_InvalidInput $ex) {

            $flashMessenger->addMessage($ex->getMessage(), 'errors');

            //redirect on another page
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_users',
                        'action' => 'index'
                            ), 'default', true);
        }
    }
    
    
    
    //DELETE
     public function deleteAction(){
      
         $request = $this->getRequest();
         
         if(!$request->isPost()|| $request->getPost('task') != 'delete'){
             
          
             
             $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_users',
                            'action' => 'index'
                                ), 'default', true);
         }
         
         $flashMessenger = $this->getHelper('FlashMessenger');
         
         try {
             
            //read $_POST
           $id = (int) $request->getPost('id');


           if ($id <= 0) {
               throw new Application_Model_Exception_InvalidInput('Invalid user id: ' . $id, 'errors');
           }

           $cmsUsersTable = new Application_Model_DbTable_CmsUsers();
           $user = $cmsUsersTable->getUserById($id);

           if (empty($user)) {

               throw new Application_Model_Exception_InvalidInput('No user is found with id: ' . $id, 'errors');

           }
           
           $cmsUsersTable->deleteUser($id);
        
        
            $flashMessenger->addMessage('User: ' . $user['first_name'] . ' ' .$user['last_name'] . 'has been deleted', 'success');
            
            //redirect on another page
            $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_users',
                            'action' => 'index'
                                ), 'default', true);
             
         } catch (Application_Model_Exception_InvalidInput $ex) {
             
             $flashMessenger->addMessage($ex->getMessage(), 'errors');
            
            //redirect on another page
            $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_users',
                            'action' => 'index'
                                ), 'default', true);
         }

    }

    //RESET PASSWORD
      public function resetpasswordAction() {

        $request = $this->getRequest();

        $id = (int) $request->getParam('id');

        if ($id <= 0) {
            //prekida se izvrsavanje proograma i prikazuje se page not found
            throw new Zend_Controller_Router_Exception('Invalid user id: ' . $id, 404);
        }
        
        
           $loggedinUser = Zend_Auth::getInstance()->getIdentity();

            if ($id == $loggedinUser['id']) {
                //redirect user to edit profile page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_profile',
                            'action' => 'changepassword'
                                ), 'default', true);
            }
 
        
        $cmsUsersTable = new Application_Model_DbTable_CmsUsers();
        $user = $cmsUsersTable->getUserById($id);

        if (empty($user)) {
            //prekida se izvrsavanje proograma i prikazuje se page not found
            throw new Zend_Controller_Router_Exception('No user is found with id ' . $id, 404);
        }


        $flashMessenger = $this->getHelper('FlashMessenger');

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors'),
        );

        

        if ($request->isPost() && $request->getPost('task') === 'resetpassword') {
            
                $cmsUsersTable->resetUserPassword($user['id']);

              
                $flashMessenger->addMessage('Password has been updated', 'success');

                //redirect to same or another page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_users',
                            'action' => 'index'
                                ), 'default', true);
            } 
        

        $this->view->systemMessages = $systemMessages;
        $this->view->form = $form;

        $this->view->user = $user;
    }
}
