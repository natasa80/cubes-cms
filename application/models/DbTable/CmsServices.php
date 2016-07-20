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
        
          
        $select = $this->select();
        
        //Sort rows by order_number DESC and fets=ch row from the top
        //with biggest order number
        $select->order('order_number DESC');
        
        $serviceWithBiggestOrderNumber = $this->fetchRow($select);
        
        
        
        if ($serviceWithBiggestOrderNumber instanceof Zend_Db_Table_Row) {
            
            $service['order_number'] = $serviceWithBiggestOrderNumber['order_number'] +1;
        } else {
            //table was empty, we are inserting first member
            $service['order_number'] = 1;
        }
        
        $id = $this->insert($service);

        return $id;
        
        
        }
    /**
     * 
     * @param int $id ID of member to delete
     */
   public function deleteService($id) {

         //member to delete
        $service = $this->getServiceById($id);
        
        $this->update(array(
            'order_number' => new Zend_Db_Expr('order_number -1')
        ),
             'order_number > ' . $service['order_number']);


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
