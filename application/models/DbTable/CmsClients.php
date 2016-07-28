<?php

class Application_Model_DbTable_CmsClients extends Zend_Db_Table_Abstract{
    
    
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    protected $_name = 'cms_clients';
    
     /**
     * 
     * @param int $id
     * @return null|array Associative array with keys as cms_members table columns or NULL if not found
     */
    public function getClientById($id) {

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

    public function updateClient($id, $client) {

        //izbegavamo da se promeni id usera, brise se iz niza ukoliko je setovan
        if (isset($client['id'])) {

            unset($client['id']);
        }
        $this->update($client, 'id = ' . $id);
    }

   
    public function insertClient($client) {
        
        
        $select = $this->select();
        
        //Sort rows by order_number DESC and fets=ch row from the top
        //with biggest order number
        $select->order('order_number DESC');
        
        $clientWithBiggestOrderNumber = $this->fetchRow($select);
        
        
        
        if ($clientWithBiggestOrderNumber instanceof Zend_Db_Table_Row) {
            
            $client['order_number'] = $clientWithBiggestOrderNumber['order_number'] +1;
        } else {
            //table was empty, we are inserting first member
            $client['order_number'] = 1;
        }
        
        $id = $this->insert($client);

        return $id;
    }

    /**
     * 
     * @param int $id ID of member to delete
     */
    public function deleteClient($id) {
        
        
        //member to delete
        $client = $this->getClientById($id);
        
        $this->update(array(
            'order_number' => new Zend_Db_Expr('order_number -1')
        ),
             'order_number > ' . $client['order_number']);


        $this->delete('id = ' . $id);
    }

    /**
     * 
     * @param nt $id ID of member to enable
     */
    public function disableClient($id) {

        $this->update(array(
            'status' => self::STATUS_DISABLED
                ), 'id = ' . $id);
    }

    /**
     * 
     * @param nt $id ID of member to enable
     */
    public function enableClient($id) {

        $this->update(array(
            'status' => self::STATUS_ENABLED
                ), 'id = ' . $id);
    }

    
    
    
    
    public function updateClientOfOrder($sortedIds) {

        foreach ($sortedIds as $orderNumber => $id) {

            $this->update(array(
                'order_number' => $orderNumber + 1 // +1 because it starts from 0
                    ), 'id = ' . $id);
        }
    }
    
    
       /**
     * 
     * @param array $members
     * @return int number of active members
     */
    public function activeClients($clients) {
       
        $activeClients = 0; 
        foreach ($clients as $client) {
            if ($clients['status'] == self::STATUS_ENABLED) {
                $activeClients ++;
            }
        }

        return $activeClients;
    }
    
    
    /**
     * 
     * @param array $members
     * @return int total number of members
     */
    public function totalClients($clients) {
        $totalNumberOfClients =0;
        
        foreach ($clients as $client){
            $totalNumberOfClients ++;
        }
        
        return $totalNumberOfClients ;
    }
    
    
}

