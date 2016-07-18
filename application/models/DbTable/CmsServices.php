 <?php

class Application_Model_DbTable_CmsServices extends Zend_Db_Table_Abstract {

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    protected $_name = 'cms_services';

    public function getServiceById($id) {

        $select = $this->select();
        $select->where('id =?', $id);

        $row = $this->fetchRow($select);

        if ($row instanceof Zend_Db_Table_Row) {

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
    public function updateService($id, $user) {

        if (isset($user['id'])) {

            unset($user['id']);
        }
        $this->update($user, 'id = ' . $id);
    }



    
    
      public function insertService($service){
        
        $maxOrderNumber = new Zend_Db_Expr('MAX(order_number) as order_number');
        $select = $this->select()
                    ->from(array(
                        'cms_services' => 'cms_services'),$maxOrderNumber );
        
        $row = $this->fetchRow($select);
        
       if(empty($row)){
           
           return FALSE;
       }else{
           $rowData = $row->toArray();
       }
       
       $newOrderNumber = $rowData['order_number']+1;
       
       $service[order_number] = $newOrderNumber;
       $id = $this->insert($service);
       
       return $id;
       
        
    }
    /**
     * 
     * @param int $id ID of member to delete
     */
   public function deleteService($id) {

        //dobijamo zeljeni red sa ID-jem koji se brise
        $select = $this->select();
        $select->where('id =?', $id);

        $row = $this->fetchRow($select);

        if ($row instanceof Zend_Db_Table_Row) {

            $row->toArray();
        } else {
            return null;
        }

        //dobijamo order number od ID-ja koji se brise
        $orderDeleted = $row['order_number'];
       

        $select = $this->select();
        $select->where('order_number > ?', $orderDeleted);

        //dobijamo sve kolone koje zadovoljavaju uslov
        $rows = $this->fetchAll($select);
        
        if (empty($rows)) {
            return FALSE;
        } else {
            $rowsData = $rows->toArray();
        }
       
        foreach ($rowsData as $row) {

            $this->update(array(
                'order_number' => $row['order_number'] - 1
                    ), 'id= ' . $row['id']);
        }

        $this->delete('id = ' . $id);
    }
    /**
     * 
     * @param nt $id ID of member to enable
     */
    public function disableService($id) {

        $this->update(array(
            'status' => self::STATUS_DISABLED
                ), 'id = ' . $id);
    }

    /**
     * 
     * @param nt $id ID of member to enable
     */
    public function enableService($id) {

        $this->update(array(
            'status' => self::STATUS_ENABLED
                ), 'id = ' . $id);
    }
    
    
    /**
     * 
     * @param nt $id ID of service to sort
     */
    public function updateServiceOfOrder($sortedIds) {

        foreach ($sortedIds as $orderNumber => $id) {

            $this->update(array(
                'order_number' => $orderNumber + 1 // +1 because it starts from 0
                    ), 'id = ' . $id);
        }
    }

}
