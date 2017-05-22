<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Sphinx Search Ultimate
 * @version   2.2.9
 * @revision  370
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_SearchSphinx_Model_Stopword extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('searchsphinx/stopword');
    }

    public function import($filePath, $stores)
    {
        if (!is_array($stores)) {
            $stores = array($stores);
        }

        $resource   = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');
        $tableName  = Mage::getSingleton('core/resource')->getTableName('searchsphinx/stopword');


        $content = file_get_contents($filePath);
        $lines   = explode("\n", $content);
        foreach ($stores as $store) {
            foreach ($lines as $value) {
                $value = strtolower($value);
                $rows[] = array(
                    'word'  => $value,
                    'store' => $store,
                );

                if (count($rows) > 1000) {
                    $connection->insertArray($tableName, array('word', 'store'), $rows);
                    $rows = array();
                }
            }

            if (count($rows) > 0) {
                $connection->insertArray($tableName, array('word', 'store'), $rows);
            }
        }

        return count($lines);
    }

    public function isStopWord($word, $store)
    {
        return $this->getCollection()->addFieldToFilter('word', $word)
            ->addFieldToFilter('store', $store)
            ->count();
    }
}