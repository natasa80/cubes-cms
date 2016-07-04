<?php

class Zend_View_Helper_SystemMessagesHtml extends Zend_View_Helper_Abstract
{
    
    public function systemMessagesHtml ($systemMessages){
        
        //resetovanje placeholdera
       $this->view->placeholder('$systemMessagesHtml')->exchangeArray(array());
        
        if (!empty($systemMessages['success'])){
            
            foreach ($systemMessages['success'] as $message){
                
            $this->view->placeholder('$systemMessagesHtml')->captureStart();//ovaj html koji smo ispisali nece se odmah ispisati, vec se stavlja pod kljucem $systemMessagesHtml
            ?>
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <?php echo $this->view->escape($message); ?>  
                </div>
            <?php
            $this->view->placeholder('$systemMessagesHtml')->captureEnd();//APENDUJE, on se samo nadovezuje na sve poruke 
                
            }
        }
        
        if (!empty($systemMessages['errors'])){
            
            foreach ($systemMessages['errors'] as $message){
              $this->view->placeholder('$systemMessagesHtml')->captureStart();
            ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?php echo $this->view->escape($message); ?>  
                </div>
            <?php
            $this->view->placeholder('$systemMessagesHtml')->captureEnd();
            }
        }
        
        return $this->view->placeholder('$systemMessagesHtml')->toString();
    }
    
    
}