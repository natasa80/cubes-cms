<?php

class Application_Model_DbTable_CmsServices extends Zend_Db_Table_Abstract
{
    
    const STATUS_ENABLED = 1;
     const STATUS_DISABLED = 0;
    
    protected $_name = 'cms_services';
    
    
    
        public function getServiceById($id){
        
        $select = $this->select();
        $select->where('id =?',  $id);
        
        $row = $this->fetchRow($select);
        
        if($row instanceof Zend_Db_Table_Row){
            
            return $row->toArray(); 
        } else {
            //row not found
            return null;
        }
        }
        
        
        /**
         * 
         * @param int $id
         * @param array $user Associative array with keys as column names and values as column new values
         */
        public function updateService($id, $user){
           
            if(isset($user['id'])){
                
                unset($user['id']);
                
            }
            $this->update($user, 'id = ' . $id);
           
        }
        
}