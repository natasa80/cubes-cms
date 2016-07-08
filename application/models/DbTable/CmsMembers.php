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
    public function insertMember($member){
        //fetch order number for new member
        
        $id = $this->insert($member);
        
        return $id;
        
        
        
    }

}
