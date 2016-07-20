<?php

class Application_Model_DbTable_CmsUsers extends Zend_Db_Table_Abstract
{
    
    const STATUS_ENABLED = 1;
     const STATUS_DISABLED = 0;
     const DEFAULT_PASSWORD = 'cubesphp';

     protected $_name = 'cms_users';//podesavamo ime tabele
    
    
    
    /** 
     * @param array $user Associative array with keys as column names and values as column new values
     * @return int ID of new user
     */
    public function insertUser($user){
        
        //set default password for new user
        $user['password'] = md5(self::DEFAULT_PASSWORD);
        
        
       return  $this->insert($user);
        
    }

    



    /**
     * 
     * @param int $id
     * @return null|array Associative array with keys as cms_users table columns or NULL if not found
     */
    public function getUserById($id){
        
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
        public function updateUser($id, $user){
            
            //izbegavamo da se promeni id usera, brise se iz niza ukoliko je setovan
            if(isset($user['id'])){
                
                unset($user['id']);
                
            }
            $this->update($user, 'id = ' . $id);
           
        }
        
        /**
         * 
         * @param int $id
         * @param string $newPassword Plain password, not hashed
         */
       public function changeUserPassword($id, $newPassword){
           //update "password" columnt , set md5 value of new password, for user with id=$id
           $this->update(array( 'password' => md5($newPassword)), 'id = ' . $id);
           
       }
       
       
    }

