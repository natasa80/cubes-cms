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
//        echo  json_encode($brandsJson);//kovertuje u json format
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
        
        $models = $brands[$brand];
        
        $modelsJson = array();
        
        foreach ($models as $modelId => $modelLabel){
            $modelsJson[] = array(
                
                'value' => $modelId,
                'label' => $modelLabel
            );
            
        }
        $this->getHelper('Json')->sendJson($modelsJson);
    }

    
    
    public function soapAction() {

        $wsdl = 'https://webservices.nbs.rs/CommunicationOfficeService1_0/ExchangeRateService.asmx?WSDL';
        $error = '';
        
        $currencyList = array();
        
        try {

            //napravimo instancu klase od wsdl i mozemo da koristimo sve njene fje
            $soapClient = new Zend_Soap_Client_DotNet($wsdl);
            
            //php soap extension 
            // klassa ugradjena u php
            
            $header = new SoapHeader(
                    'http://communicationoffice.nbs.rs', //namespace
                    'AuthenticationHeader',              //atribut name
                    array(
                        'UserName' => '',
                        'Password' => '',
                        'LicenceID' => '',
                    )
                    
                    ); 
            $soapClient->addSoapInputHeader($header);

            $responseRaw = $soapClient->GetCurrentExchangeRate(array(
                'exchangeRateListTypeID' => 1
            ));
            
           if($responseRaw ->any){//ovde moramo da ga isparsiramo da bi smo vidjeli njegov sadrzas/strukkturu
               $response = simplexml_load_string($responseRaw ->any);
               
               if ($response->ExchangeRateDataSet && $response->ExchangeRateDataSet->ExchangeRate) {
                   $currencyList = $response->ExchangeRateDataSet->ExchangeRate;
               }
           }
            
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        
        $this->view->soapClient = $soapClient;
        $this->view->response = $response;
        $this->view->error = $error;
        $this->view->currencyList = $currencyList;
    }
}
