<?php

class Admin_TestController extends Zend_Controller_Action {

    public function indexAction() {
        
    }

    public function jsintroAction() {
        
    }

    public function jqueryAction() {
        
    }

    public function ajaxintroAction() {
        
    }

    public function ajaxbrandsAction() {


        $brands = array(
            'fiat' => array(
                'punto' => 'Punto',
                'stilo' => 'Stilo',
                '500l' => '500 L'
            ),
            'opel' => array(
                'corsa' => 'Corsa',
                'astra' => 'Astra',
                'vectra' => 'Vectra',
                'insignia' => 'Insignia'
            ),
            'renault' => array(
                'twingo' => 'Twingo',
                'clio' => 'Clio',
                'megane' => 'Megane',
                'scenic' => 'Scenic'
            )
        );
        
        
        $brandsJson = array();
        
        foreach ($brands as $brand=>$models){
            $brandsJson[] = array(
                'value' => $brand,
                'label' => ucfirst($brand)
            );
         
        }
        
        
//        //disable LAyout
//        Zend_Layout::getMvcInstance()->disableLayout();
//        
//        //disable view script rendering
//        $this->getHelper('ViewRenderer')->setNoRender(true);
//        
//        //set content type as application json
//        header('Content-Type: application/json');
//        
//        echo  json_encode($brandsJson);
//        
//        
        //ovaj helper disaluje laypout, renderuje script i postavlja vrednost
        $this->getHelper('Json')->sendJson($brandsJson);
      
    }

    public function ajaxmodelsAction() {


        $brands = array(
            'fiat' => array(
                'punto' => 'Punto',
                'stilo' => 'Stilo',
                '500l' => '500 L'
            ),
            'opel' => array(
                'corsa' => 'Corsa',
                'astra' => 'Astra',
                'vectra' => 'Vectra',
                'insignia' => 'Insignia'
            ),
            'renault' => array(
                'twingo' => 'Twingo',
                'clio' => 'Clio',
                'megane' => 'Megane',
                'scenic' => 'Scenic'
            )
        );
        
        $request = $this->getRequest();
        
        $brand = $request->getParam('brand');
        
        //ovde proveravamo da li je setovan kljuc u nizu
        if (!isset($brands[$brand])){
            
            throw new Zend_Controller_Router_Exception('Unknown brand', 404);
        }
    }

}
