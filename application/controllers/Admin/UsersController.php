<?php

class Admin_UsersController extends Zend_Controller_Action {

    public function indexAction() {

        $flashMessenger = $this->getHelper('FlashMessenger');

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors'),
        );




        $this->view->users = array();
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

            //sve sto dolazi na server smesteno je u Request objekat

            $request instanceof Zend_Controller_Request_Http;



            if ($request->isXmlHttpRequest()) {
                //request is ajax request
                //send response as json

                $responseJson = array(
                    'status' => 'ok',
                    'statusMessage' => 'User: ' . $user['first_name'] . ' ' . $user['last_name'] . ' has been disabled'
                );

                //send json as response
                $this->getHelper('Json')->sendJson($responseJson);
            } else {
                //request is not ajax
                //send message over session-flash message
                //and do redirect
                $flashMessenger->addMessage('User: ' . $user['first_name'] . ' ' . $user['last_name'] . ' has been disabled', 'success');

                //redirect on another page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_users',
                            'action' => 'index'
                                ), 'default', true);
            }
        } catch (Application_Model_Exception_InvalidInput $ex) {

            if ($request->isXmlHttpRequest()) {

                $responseJson = array(
                    'status' => 'ok',
                    'statusMessage' => $ex->getMessage()
                );
                //send json as response
                $this->getHelper('Json')->sendJson($responseJson);
            } else {
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

            //Sve sto je poslato na server kao zahtev uzimamo preko request objekta
            $request instanceof Zend_Controller_Request_Http;


            //ispitujemo da li je zahtev ajax ili ne
            if ($request->isXmlHttpRequest()) {

                //ukoiko je zahtev ajax pravimo odgovor u json formatu
                $responseJson = array(
                    'status' => 'ok',
                    'statusMessage' => 'User: ' . $user['first_name'] . ' ' . $user['last_name'] . ' has been enabled'
                );

                //saljemo odgovor kao ajax, preko Hleper-a
                $this->getHelper('Json')->sendJson($responseJson);
            } else {
                //ukoliko zahtev nije ajax onda saljemo standardan odgovor
                $flashMessenger->addMessage('User: ' . $user['first_name'] . ' ' . $user['last_name'] . ' has been enabled', 'success');

                //redirect on another page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_users',
                            'action' => 'index'
                                ), 'default', true);
            }
        } catch (Application_Model_Exception_InvalidInput $ex) {
            //ukoliko je uhvacena neka greska 
            //ponovo ispitujemo da li je ajax zahtev
            if ($request->isXmlHttpRequest()) {
                //ukoliko jeste gresku prenosimo preko json-a

                $responseJson = array(
                    'status' => 'ok',
                    'statusMessage' => $ex->getMessage()
                );
                //send json as response
                $this->getHelper('Json')->sendJson($responseJson);
            } else {
                //ukoliko nije prenosimo standardno

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
    }

    //DELETE
    public function deleteAction() {

        $request = $this->getRequest();

        if (!$request->isPost() || $request->getPost('task') != 'delete') {



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
            $request instanceof Zend_Controller_Request_Http;

            if ($request->isXmlHttpRequest()) {
                $responseJson = array(
                    'status' => 'ok',
                    'statusMessage' => 'User: ' . $user['first_name'] . ' ' . $user['last_name'] . ' has been deleted'
                );

                //saljemo odgovor kao ajax, preko Hleper-a
                $this->getHelper('Json')->sendJson($responseJson);
            } else {
                $flashMessenger->addMessage('User: ' . $user['first_name'] . ' ' . $user['last_name'] . ' has been deleted', 'success');

                //redirect on another page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_users',
                            'action' => 'index'
                                ), 'default', true);
            }
        } catch (Application_Model_Exception_InvalidInput $ex) {
            if ($request->isXmlHttpRequest()) {
                $responseJson = array(
                    'status' => 'ok',
                    'statusMessage' => $ex->getMessage()
                );
                //send json as response
                $this->getHelper('Json')->sendJson($responseJson);
            } else {

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

            $request instanceof Zend_Controller_Request_Http;

            if ($request->isXmlHttpRequest()) {
                $responseJson = array(
                    'status' => 'ok',
                    'statusMessage' => 'Users password: ' . $user['first_name'] . ' ' . $user['last_name'] . ' has been reset'
                );
                $this->getHelper('Json')->sendJson($responseJson);
            } else {
                $flashMessenger->addMessage('Password has been updated', 'success');

                //redirect to same or another page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_users',
                            'action' => 'index'
                                ), 'default', true);
            }
        }


        $this->view->systemMessages = $systemMessages;
        $this->view->form = $form;

        $this->view->user = $user;
    }

    public function datatableAction() {

        $request = $this->getRequest();
        //dohvatamo sve parametre koji su poslati 
        $datatableParameters = $request->getParams();




        /*

          Array
          (
          [controller] => admin_users
          [action] => datatable
          [module] => default

          [draw] => 2

          [order] => Array
          (
          [0] => Array
          (
          [column] => 2
          [dir] => asc
          )

          )

          [start] => 0
          [length] => 5
          [search] => Array
          (
          [value] =>
          [regex] => false
          )

          )

         */

        $cmsUsersTable = new Application_Model_DbTable_CmsUsers();

        //defaultne vrednosti
        $loggedInUser = Zend_Auth::getInstance()->getIdentity();

        $filters = array(
            'id_exclude' => $loggedInUser
        );

        $orders = array();
        $limit = 5;
        $page = 1;
        $draw = 1;

        //mora biti osti raspored kao i u view scripti 
        $columns = array('status', 'username', 'first_name', 'last_name', 'email', 'actions');


        //Process datateble parameters

        if (isset($datatableParameters['draw'])) {

            $draw = $datatableParameters['draw'];

            if (isset($datatableParameters['length'])) {

                $limit = $datatableParameters['length'];


                if (isset($datatableParameters['start'])) {
                    $page = floor($datatableParameters['start'] / $datatableParameters['length']) + 1;
                }
            }
        }



        if (
                isset($datatableParameters['order']) && is_array($datatableParameters['order'])
        ) {

            foreach ($datatableParameters['order'] as $dataTableOrder) {

                $columnIndex = $dataTableOrder['column'];
                $orderDirection = strtoupper($dataTableOrder['dir']);

                if (isset($columns[$columnIndex])) {
                    $orders[$columns[$columnIndex]] = $orderDirection;
                }
            }
        }


        if (
                isset($datatableParameters['search']) && is_array($datatableParameters['search']) && isset($datatableParameters['search']['value'])
        ) {
            $filters['username_search'] = $datatableParameters['search']['value'];
        }



        $users = $cmsUsersTable->search(array(
            'filters' => $filters,
            'orders' => $orders,
            'limit' => $limit,
            'page' => $page
        ));


        //prikazuju  se samo 
        $usersFilteredCount = $cmsUsersTable->count($filters);
        $usersTotal = $cmsUsersTable->count();

        $this->view->users = $users;
        $this->view->usersFilteredCount = $usersFilteredCount;
        $this->view->usersTotal = $usersTotal;
        $this->view->draw = $draw;
        $this->view->columns = $columns;
    }

    public function dashboardAction() {

        $cmsUsersDbTable = new Application_Model_DbTable_CmsUsers();
         
      
        $totalNumberOfUsers = $cmsUsersDbTable->count();
        $activeUsers = $cmsUsersDbTable->count(array(
            'status' => Application_Model_DbTable_CmsUsers::STATUS_ENABLED
        ));
      
        $this->view->activeUsers = $activeUsers;
        $this->view->totalNumberOfUsers = $totalNumberOfUsers;
        
    }

}
