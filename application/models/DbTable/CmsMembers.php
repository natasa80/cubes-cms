<?php

class Application_Model_DbTable_CmsMembers extends Zend_Db_Table_Abstract {

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    protected $_name = 'cms_members';

    /**
     * 
     * @param int $id
     * @return null|array Associative array with keys as cms_members table columns or NULL if not found
     */
    public function getMemberById($id) {

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

    public function updateMember($id, $member) {

        //izbegavamo da se promeni id usera, brise se iz niza ukoliko je setovan
        if (isset($member['id'])) {

            unset($member['id']);
        }
        $this->update($member, 'id = ' . $id);
    }

    /**
     * 
     * @param array $member
     * @return int The new ID of new member (autoincrement)
     */

    public function insertMember($member) {
        
        
        $select = $this->select();
        
        //Sort rows by order_number DESC and fets=ch row from the top
        //with biggest order number
        $select->order('order_number DESC');
        
        $memebrWithBiggestOrderNumber = $this->fetchRow($select);
        
        
        
        if ($memebrWithBiggestOrderNumber instanceof Zend_Db_Table_Row) {
            
            $member['order_number'] = $memebrWithBiggestOrderNumber['order_number'] +1;
        } else {
            //table was empty, we are inserting first member
            $member['order_number'] = 1;
        }
        
        $id = $this->insert($member);

        return $id;
    }

    /**
     * 
     * @param int $id ID of member to delete
     */
    public function deleteMember($id) {
        
        $memberPhotoFilePath = PUBLIC_PATH . '/uploads/members/' . $id . '.jpg';
        
        if(is_file($memberPhotoFilePath)){
            unlink($memberPhotoFilePath);
        }
        
        //member to delete
        $member = $this->getMemberById($id);
        
        $this->update(array(
            'order_number' => new Zend_Db_Expr('order_number -1')
        ),
             'order_number > ' . $member['order_number']);


        $this->delete('id = ' . $id);
    }

    /**
     * 
     * @param nt $id ID of member to enable
     */
    public function disableMember($id) {

        $this->update(array(
            'status' => self::STATUS_DISABLED
                ), 'id = ' . $id);
    }

    /**
     * 
     * @param nt $id ID of member to enable
     */
    public function enableMember($id) {

        $this->update(array(
            'status' => self::STATUS_ENABLED
                ), 'id = ' . $id);
    }

    
    
    
    
    public function updateMemberOfOrder($sortedIds) {

        foreach ($sortedIds as $orderNumber => $id) {

            $this->update(array(
                'order_number' => $orderNumber + 1 // +1 because it starts from 0
                    ), 'id = ' . $id);
        }
    }
    
    
    

}
