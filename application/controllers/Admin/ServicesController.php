
<?php

class Admin_ServicesController extends Zend_Controller_Action {
    
    public function indexAction() {

        $flashMessenger = $this->getHelper('FlashMessenger');

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors'),
        );
        
        $cmsServicesDbTable = new Application_Model_DbTable_CmsServices();


        //select je objekat klase Zend_Db_ Select
        $select = $cmsServicesDbTable->select();



        $services = $cmsServicesDbTable->fetchAll($select);
        $this->view->services = $services;
        $this->view->systemMessages = $systemMessages;
    }

    
    
    public function addAction() {
        


        $request = $this->getRequest(); //podaci iz url-a iz forme koje dobijemo
        $flashMessenger = $this->getHelper('FlashMessenger');

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors'),
        );

        $form = new Application_Form_Admin_ServiceAdd();

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
                $cmsServicesTable = new Application_Model_DbTable_CmsServices();
                $cmsServicesTable->insertService($formData);
                // do actual task
                //save to database etc
                //set system message
                $flashMessenger->addMessage('Service has been saved', 'success');

                //redirect to same or another page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_services',
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
            throw new Zend_Controller_Router_Exception('Invalid service id: ' . $id, 404);
        }


        $cmsServicesTable = new Application_Model_DbTable_CmsServices();
        $service = $cmsServicesTable->getServiceById($id);

        if (empty($service)) {
            //prekida se izvrsavanje proograma i prikazuje se page not found
            throw new Zend_Controller_Router_Exception('No service is found with id ' . $id, 404);
        }


        $flashMessenger = $this->getHelper('FlashMessenger');

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors'),
        );

        $form = new Application_Form_Admin_ServiceAdd();

        //default form data//mi nemamo default vrednosti
        $form->populate($service);



        if ($request->isPost() && $request->getPost('task') === 'update') {//ispitujemo da lije pokrenuta forma
            try {

                //check form is valid
                if (!$form->isValid($request->getPost())) {//sve sto je u post zahtevu prosledi formi na validaciju
                    throw new Application_Model_Exception_InvalidInput('Invalid data was sent for  service' );
                }

                //get form data//vrednosti iz forme se uzimaju preko getVaues i upisuju u niz
                //to su filtrirani i validirani podaci
                $formData = $form->getValues();


                //iUpdate postojeceg zapisa u tabeli
                
                $cmsServicesTable->updateService($service['id'], $formData);
                
                // do actual task
                //save to database etc
                //set system message
                $flashMessenger->addMessage('Service has been updated', 'success');

                //redirect to same or another page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_services',
                            'action' => 'index'
                                ), 'default', true);
            } catch (Application_Model_Exception_InvalidInput $ex) {
                $systemMessages['errors'][] = $ex->getMessage();
            }
        }

        $this->view->systemMessages = $systemMessages;
        $this->view->form = $form;

        $this->view->service = $service;
    }

}
