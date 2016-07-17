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
//    public function insertMember($member){
//        //fetch order number for new member
//        
//        $id = $this->insert($member);
//        
//        return $id;
//       
//    }



    public function insertMember($member) {

        $maxOrderNumber = new Zend_Db_Expr('MAX(order_number) as order_number');

        $select = $this->select()
                ->from(array(
            'cms_members' => 'cms_members'), $maxOrderNumber);


        $row = $this->fetchRow($select);

        if (empty($row)) {

            return FALSE;
        } else {
            $rowData = $row->toArray();
        }

        $newOrderNumber = $rowData['order_number'] + 1;

        $member[order_number] = $newOrderNumber;
        $id = $this->insert($member);

        return $id;
    }

    /**
     * 
     * @param int $id ID of member to delete
     */
    public function deleteMember($id) {

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
        // print_r($orderDeleted);

        $select = $this->select();
        $select->where('order_number > ?', $orderDeleted);

        //dobijamo sve kolone koje zadovoljavaju uslov
        $rows = $this->fetchAll($select);
        // print_r($rows);

        if (empty($rows)) {
            return FALSE;
        } else {
            $rowsData = $rows->toArray();
        }
        // print_r($rowsData);


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
