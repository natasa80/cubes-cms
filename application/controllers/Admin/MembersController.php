<?php

class Admin_MembersController extends Zend_Controller_Action {

    public function indexAction() {

        $flashMessenger = $this->getHelper('FlashMessenger');

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors'),
        );
        //prikaz svih member-a
        $cmsMembersDbTable = new Application_Model_DbTable_CmsMembers();

        $members = $cmsMembersDbTable->search(array(
//            'filters' => array(
//                'first_name' => array('Aleksandar', 'Aleksandra', 'Bojan')
                
           // ),
            'orders' => array(
                'order_number' => 'ASC',
                
            ),
            //'limit' => 4,
            //'page' => 2
        ));
       
        $this->view->members = $members;
        $this->view->systemMessages = $systemMessages;
    }

    public function addAction() {
        //prikaz svih member-a


        $request = $this->getRequest(); //podaci iz url-a iz forme koje dobijemo
        $flashMessenger = $this->getHelper('FlashMessenger');

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors'),
        );

        $form = new Application_Form_Admin_MemberAdd();

//default form data//mi nemamo default vrednosti
        $form->populate(array(
        ));



        if ($request->isPost() && $request->getPost('task') === 'save') {//ispitujemo da lije pokrenuta forma
            try {

                //check form is valid
                if (!$form->isValid($request->getPost())) {//sve sto je u post zahtevu prosledi formi na validaciju
                    throw new Application_Model_Exception_InvalidInput('Invalid data was sent for new member');
                }

                //get form data//vrednosti iz forme se uzimaju preko getVaues i upisuju u niz
                //to su filtrirani i validirani podaci
                $formData = $form->getValues();


                //remove key memebr_photo from form data because there is no column memebr_photo in cms _members
                unset($formData['member_photo']);



                //insertujemo zapis u bazu
                $cmsMembersTable = new Application_Model_DbTable_CmsMembers();

                //insert member returns ID of the new member
                $memberId = $cmsMembersTable->insertMember($formData);

                if ($form->getElement('member_photo')->isUploaded()) {
                    //photo is uploaded 

                    $fileInfos = $form->getElement('member_photo')->getFileInfo('member_photo');
                    $fileInfo = $fileInfos['member_photo'];
                    //$fileInfo = $_FILES["member_photo"];


                    try {
                        //open uploaded photo in temporary directory
                        $memberPhoto = Intervention\Image\ImageManagerStatic::make($fileInfo['tmp_name']);

                        $memberPhoto->fit(150, 150);
                        $memberPhoto->save(PUBLIC_PATH . '/uploads/members/' . $memberId . '.jpg');
                    } catch (Exception $ex) {

                        $flashMessenger->addMessage('Member has been saved, but error occured during image processing', 'errors');

                        //redirect to same or another page
                        $redirector = $this->getHelper('Redirector');
                        $redirector->setExit(true)
                                ->gotoRoute(array(
                                    'controller' => 'admin_members',
                                    'action' => 'edit',
                                    'id' => $memberId
                                        ), 'default', true);
                    }
                }


                // do actual task
                //save to database etc
                //set system message
                $flashMessenger->addMessage('Member has been saved', 'success');

                //redirect to same or another page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_members',
                            'action' => 'index'
                                ), 'default', true);
            } catch (Application_Model_Exception_InvalidInput $ex) {
                $systemMessages['errors'][] = $ex->getMessage();
            }
        }

        $this->view->systemMessages = $systemMessages;
        $this->view->form = $form;
    }

    public function editAction() {


        $request = $this->getRequest();

        $id = (int) $request->getParam('id');

        if ($id <= 0) {
            //prekida se izvrsavanje proograma i prikazuje se page not found
            throw new Zend_Controller_Router_Exception('Invalid member id: ' . $id, 404);
        }


        $cmsMembersTable = new Application_Model_DbTable_CmsMembers();
        $member = $cmsMembersTable->getMemberById($id);

        if (empty($member)) {
            //prekida se izvrsavanje proograma i prikazuje se page not found
            throw new Zend_Controller_Router_Exception('No member is found with id ' . $id, 404);
        }


        $flashMessenger = $this->getHelper('FlashMessenger');

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors'),
        );

        $form = new Application_Form_Admin_MemberAdd();

        //default form data//mi nemamo default vrednosti
        $form->populate($member);



        if ($request->isPost() && $request->getPost('task') === 'update') {//ispitujemo da lije pokrenuta forma
            try {

                //check form is valid
                if (!$form->isValid($request->getPost())) {//sve sto je u post zahtevu prosledi formi na validaciju
                    throw new Application_Model_Exception_InvalidInput('Invalid data was sent for  member');
                }


                $formData = $form->getValues();
                unset($formData['member_photo']);

                if ($form->getElement('member_photo')->isUploaded()) {
                    //photo is uploaded 

                    $fileInfos = $form->getElement('member_photo')->getFileInfo('member_photo');
                    $fileInfo = $fileInfos['member_photo'];
                    //$fileInfo = $_FILES["member_photo"];


                    try {
                        //open uploaded photo in temporary directory
                        $memberPhoto = Intervention\Image\ImageManagerStatic::make($fileInfo['tmp_name']);

                        $memberPhoto->fit(150, 150);

                        $memberPhoto->save(PUBLIC_PATH . '/uploads/members/' . $member['id'] . '.jpg');
                    } catch (Exception $ex) {

                        throw new Application_Model_Exception_InvalidInput('Error occured during image processing');
                    }
                }


                $cmsMembersTable->updateMember($member['id'], $formData);

                // do actual task
                //save to database etc
                //set system message
                $flashMessenger->addMessage('Member has been updated', 'success');

                //redirect to same or another page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_members',
                            'action' => 'index'
                                ), 'default', true);
            } catch (Application_Model_Exception_InvalidInput $ex) {
                $systemMessages['errors'][] = $ex->getMessage();
            }
        }

        $this->view->systemMessages = $systemMessages;
        $this->view->form = $form;

        $this->view->member = $member;
    }

    public function deleteAction() {

        $request = $this->getRequest();

        if (!$request->isPost() || $request->getPost('task') != 'delete') {

            //request is not post, 
            //or task is not delete
            //redirecting to index page

            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_members',
                        'action' => 'index'
                            ), 'default', true);
        }

        $flashMessenger = $this->getHelper('FlashMessenger');

        try {



            //read $_POST
            $id = (int) $request->getPost('id');


            if ($id <= 0) {
                throw new Application_Model_Exception_InvalidInput('Invalid member id: ' . $id, 'errors');
            }

            $cmsMembersTable = new Application_Model_DbTable_CmsMembers();
            $member = $cmsMembersTable->getMemberById($id);

            if (empty($member)) {

                throw new Application_Model_Exception_InvalidInput('No member is found with id: ' . $id, 'errors');
            }

            $cmsMembersTable->deleteMember($id);


            $flashMessenger->addMessage('Member: ' . $member['first_name'] . ' ' . $member['last_name'] . 'has been deleted', 'success');

            //redirect on another page
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_members',
                        'action' => 'index'
                            ), 'default', true);
        } catch (Application_Model_Exception_InvalidInput $ex) {

            $flashMessenger->addMessage($ex->getMessage(), 'errors');

            //redirect on another page
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_members',
                        'action' => 'index'
                            ), 'default', true);
        }
    }

    public function disableAction() {

        $request = $this->getRequest();

        if (!$request->isPost() || $request->getPost('task') != 'disable') {

            //request is not post, 
            //or task is not delete
            //redirecting to index page

            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_members',
                        'action' => 'index'
                            ), 'default', true);
        }

        $flashMessenger = $this->getHelper('FlashMessenger');

        try {



            //read $_POST
            $id = (int) $request->getPost('id');


            if ($id <= 0) {
                throw new Application_Model_Exception_InvalidInput('Invalid member id: ' . $id, 'errors');
            }

            $cmsMembersTable = new Application_Model_DbTable_CmsMembers();
            $member = $cmsMembersTable->getMemberById($id);

            if (empty($member)) {

                throw new Application_Model_Exception_InvalidInput('No member is found with id: ' . $id, 'errors');
            }

            $cmsMembersTable->disableMember($id);


            $flashMessenger->addMessage('Member: ' . $member['first_name'] . ' ' . $member['last_name'] . 'has been disabled', 'success');

            //redirect on another page
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_members',
                        'action' => 'index'
                            ), 'default', true);
        } catch (Application_Model_Exception_InvalidInput $ex) {

            $flashMessenger->addMessage($ex->getMessage(), 'errors');

            //redirect on another page
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_members',
                        'action' => 'index'
                            ), 'default', true);
        }
    }

    public function enableAction() {

        $request = $this->getRequest();

        if (!$request->isPost() || $request->getPost('task') != 'enable') {

            //request is not post, 
            //or task is not delete
            //redirecting to index page

            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_members',
                        'action' => 'index'
                            ), 'default', true);
        }

        $flashMessenger = $this->getHelper('FlashMessenger');

        try {



            //read $_POST
            $id = (int) $request->getPost('id');


            if ($id <= 0) {
                throw new Application_Model_Exception_InvalidInput('Invalid member id: ' . $id, 'errors');
            }

            $cmsMembersTable = new Application_Model_DbTable_CmsMembers();
            $member = $cmsMembersTable->getMemberById($id);

            if (empty($member)) {

                throw new Application_Model_Exception_InvalidInput('No member is found with id: ' . $id, 'errors');
            }

            $cmsMembersTable->enableMember($id);


            $flashMessenger->addMessage('Member: ' . $member['first_name'] . ' ' . $member['last_name'] . 'has been enabled', 'success');

            //redirect on another page
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_members',
                        'action' => 'index'
                            ), 'default', true);
        } catch (Application_Model_Exception_InvalidInput $ex) {

            $flashMessenger->addMessage($ex->getMessage(), 'errors');

            //redirect on another page
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_members',
                        'action' => 'index'
                            ), 'default', true);
        }
    }

    public function updateorderAction() {

        $request = $this->getRequest();

        if (!$request->isPost() || $request->getPost('task') != 'saveOrder') {

            //request is not post, 
            //or task is not saveOrder
            //redirecting to index page

            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_members',
                        'action' => 'index'
                            ), 'default', true);
        }

        $flashMessenger = $this->getHelper('FlashMessenger');


        try {

            $sortedIds = $request->getPost('sorted_ids');


            if (empty($sortedIds)) {

                throw new Application_Model_Exception_InvalidInput('Sorted ids are not sent');
            }

            $sortedIds = trim($sortedIds, ' ,');

            if (!preg_match('/^[0-9]+(,[0-9]+)*$/', $sortedIds)) {
                throw new Application_Model_Exception_InvalidInput('Invalid  sorted ids ' . $sortedIds);
            }

            $sortedIds = explode(',', $sortedIds);

            $cmsMembersTable = new Application_Model_DbTable_CmsMembers();

            $cmsMembersTable->updateMemberOfOrder($sortedIds);
            

            $flashMessenger->addMessage('Order is successfully saved', 'success');

            //redirect on another page
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_members',
                        'action' => 'index'
                            ), 'default', true);
        } catch (Application_Model_Exception_InvalidInput $ex) {

            $flashMessenger->addMessage($ex->getMessage(), 'errors');

            //redirect on another page
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_members',
                        'action' => 'index'
                            ), 'default', true);
        }
    }

    public function dashboardAction() {
        
        
        $cmsMembersDbTable = new Application_Model_DbTable_CmsMembers();
        $totalNumberOfMembers = $cmsMembersDbTable->count();
        $activeMembers = $cmsMembersDbTable->count(array(
            'status' => Application_Model_DbTable_CmsMembers::STATUS_ENABLED
        ));
        
        $this->view->activeMembers = $activeMembers;
        $this->view->totalNumberOfMembers = $totalNumberOfMembers;
      
    }

}
