<?php

class Application_Model_DbTable_CmsSitemapPages extends Zend_Db_Table_Abstract{
    
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    
    protected $_name = 'cms_sitemap_pages';
    
     /**
     * 
     * @param int $id
     * @return null|array Associative array with keys as cms_sitemap_pages table columns or NULL if not found
     */
    public function getSitemapPageById($id) {

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

    public function updateSitemapPage($id, $sitemapPage) {

        //izbegavamo da se promeni id usera, brise se iz niza ukoliko je setovan
        if (isset($sitemapPage['id'])) {

            unset($sitemapPage['id']);
        }
        $this->update($sitemapPage, 'id = ' . $id);
    }

    /**
     * 
     * @param array $sitemapPage
     * @return int The new ID of new sitemapPage (autoincrement)
     */
    public function insertSitemapPage($sitemapPage) {


        $select = $this->select();

        //Sort rows by order_number DESC and fets=ch row from the top
        //with biggest order number
        $select->where('parent_id = ?', $sitemapPage['parent_id'])
                ->order('order_number DESC');

        $sitemapPageWithBiggestOrderNumber = $this->fetchRow($select);

        if ($sitemapPageWithBiggestOrderNumber instanceof Zend_Db_Table_Row) {

            $sitemapPage['order_number'] = $sitemapPageWithBiggestOrderNumber['order_number'] + 1;
        } else {
            //table was empty, we are inserting first sitemapPage
            $sitemapPage['order_number'] = 1;
        }

        $id = $this->insert($sitemapPage);

        return $id;
    }

    /**
     * 
     * @param int $id ID of sitemapPage to delete
     */
    public function deleteSitemapPage($id) {


        //sitemapPage to delete
        $sitemapPage = $this->getSitemapPageById($id);

        $this->update(array(
            'order_number' => new Zend_Db_Expr('order_number -1')
                ), 'order_number > ' . $sitemapPage['order_number'] . 'AND parent_id = ' . $sitemapPage['parent_id']);


        $this->delete('id = ' . $id);
    }

    /**
     * 
     * @param nt $id ID of sitemapPage to enable
     */
    public function disableSitemapPage($id) {

        $this->update(array(
            'status' => self::STATUS_DISABLED
                ), 'id = ' . $id);
    }

    /**
     * 
     * @param nt $id ID of sitemapPage to enable
     */
    public function enableSitemapPage($id) {

        $this->update(array(
            'status' => self::STATUS_ENABLED
                ), 'id = ' . $id);
    }

    public function updateSitemapPageOfOrder($sortedIds) {

        foreach ($sortedIds as $orderNumber => $id) {

            $this->update(array(
                'order_number' => $orderNumber + 1 // +1 because it starts from 0
                    ), 'id = ' . $id);
        }
    }
    
    
    
    /**
     * 
     * @param array $sitemapPages
     * @return int number of active sitemapPages
     */
    public function activeSitemapPages($sitemapPages) {
        $activeSitemapPages = 0;
        foreach ($sitemapPages as $sitemapPage) {
            if ($sitemapPage['status'] == self::STATUS_ENABLED) {
                $activeSitemapPages ++;
            }
        }

        return $activeSitemapPages;
    }
    
    
    /**
     * 
     * @param array $sitemapPages
     * @return int total number of sitemapPages
     */
    public function totalSitemapPages( $sitemapPages) {
        $totalNumberOfSitemapPages =0;
        
        foreach ($sitemapPages as $sitemapPage){
            $totalNumberOfSitemapPages ++;
        }
        
        
        return $totalNumberOfSitemapPages ;
    }

    
    
}