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


        //select je objekat klase Zend_Db_ Select
        $select = $cmsMembersDbTable->select();

        $select->order('order_number');


        //debug za db select - vraca se sql upit
        //die($select->assemble());


        $members = $cmsMembersDbTable->fetchAll($select);
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


                //insertujemo zapis u bazu
                $cmsMembersTable = new Application_Model_DbTable_CmsMembers();
                $cmsMembersTable->insert($formData);
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
                    throw new Application_Model_Exception_InvalidInput('Invalid data was sent for  member' );
                }

                //get form data//vrednosti iz forme se uzimaju preko getVaues i upisuju u niz
                //to su filtrirani i validirani podaci
                $formData = $form->getValues();


                //iUpdate postojeceg zapisa u tabeli
                
                $cmsMembersTable->update($formData, 'id = ' . $member['id']);
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

}
