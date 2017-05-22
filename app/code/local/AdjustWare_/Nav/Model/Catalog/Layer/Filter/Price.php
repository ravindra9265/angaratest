<?php
/**
 * Product:     Layered Navigation Pro for Magento 20/04/11
 * Package:     AdjustWare_Nav_2.1.1_0.1.4_8_74006
 * Purchase ID: QgBMpw5YKeeQ8cDxt9xj7DfR5rzlpaEBHoIuAZvOxv
 * Generated:   2011-06-08 07:09:19
 * File path:   app/code/local/AdjustWare/Nav/Model/Catalog/Layer/Filter/Price.php
 * Copyright:   (c) 2011 AITOC, Inc.
 */
?>
<?php

/**
* DIFFERENT CLASSED FOR DIFFERENT VERSIONS!
*/


    class AdjustWare_Nav_Model_Catalog_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price
    {
        protected $baseSelect = null;

        public function __construct()
        {
            parent::__construct();
        }

        protected function _getItemsData()
        {
            $data = array();
            $style = Mage::getStoreConfig('design/adjnav/price_style');
            if ('default' == $style)
            {
                if ($this->getMaxPriceInt())
                {
                    if ($this->getAttributeModel()->getAttributeCode() == 'price')
                    {
                        return parent::_getItemsData();
                    }
                    else
                    {
                        return $this->_getDecimalItemsData();
                    }
                }
                else
                {
                    return array();
                }
            }
            elseif('input' == $style){
                list($from, $to) = $this->getFilterValueFromRequest();
                $data[] = array(
                    'label' => '',
                    'value' => $from . ',' . $to,
                    'count' => 1,
                );
            }
            elseif('slider' == $style){
                $data[] = array(
                    'label' => '',
                    'value' => 0 . ',' . $this->getMaxPriceInt()+1,
                    'count' => 1,
                );
            }

            return $data;
        }

        protected function _getCacheKey()
        {
            $key = parent::_getCacheKey();
            $key .= Mage::getStoreConfig('design/adjnav/price_style');
            $key .= Mage::helper('adjnav')->getCacheKey('price');
            return $key;
        }

        private function getFilterValueFromRequest()
        {
			//	Price range code modified to work properly on first time page load by Vaseem
            //$filter = Mage::helper('adjnav')->getParam($this->_requestVar);
			$filter	=	Mage::app()->getRequest()->getParam('price');

            if (!$filter) {
                return array(0, 0);
            }

            $filter = explode(',', $filter);

            if (count($filter) != 2) {
                return array(0, 0);
            }

            list($from, $to) = $filter;
            $from = sprintf("%.02f", $from);
            $to   = sprintf("%.02f", $to);

            return array($from, $to);
        }

        /**
         * Apply price range filter to collection
         *
         * @return Mage_Catalog_Model_Layer_Filter_Price
         */
        public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
        {
            list($from, $to) = $this->getFilterValueFromRequest();

            if ('default' == Mage::getStoreConfig('design/adjnav/price_style'))
            {
                $attribute    = $this->getAttributeModel();
                /*
                if ($attribute->getAttributeCode() != 'price' AND 1 == 2)
                {

                    // start decimal
                    parent::apply($request, $filterBlock);

                    $filter = $request->getParam($this->getRequestVar());
                    if (!$filter) {
                        return $this;
                    }

                    $filter = explode(',', $filter);
                    if (count($filter) != 2) {
                        return $this;
                    }

                    list($index, $range) = $filter;
                    if ((int)$index && (int)$range) {
                        $this->setRange((int)$range);

                        $this->_getResource()->applyFilterToCollection($this, $range, $index);
                        $this->getLayer()->getState()->addFilter(
                            $this->_createItem($this->_renderItemLabel($range, $index), $filter)
                        );

                        $this->_items = array();
                    }

                    return $this;
                    // finish decimal
                }
                */

                $index = $from;
                $rate  = $to;

                $from = ($index-1)*$rate;
                $to   = $index*$rate;

                $this->setActiveState(sprintf('%d,%d', $index, $rate));
            }
            else{
                $this->setActiveState($from . ','. $to);
            }

            $this->baseSelect = clone $this->getLayer()->getProductCollection()->getSelect();
            if ($from >= 0.01 || $to >= 0.01) {
                $this->applyFromToFilter($from, $to);

            }

            return $this;
        }

        // copied from Mage_CatalogIndex_Model_Mysql4_Price,
        // bacause of AWFULL!!! design: the method accept $range, $index INSTEAD OF $from, $to args
        // hope you, my reader, understand that is it incorrect in terms of tiers (library
        // function shouldn't know about logic of price ranges ...
        protected function applyFromToFilter($from, $to)
        {
            $attribute    = $this->getAttributeModel();

            $tableAlias = $attribute->getAttributeCode() . '_idx';

            if ($attribute->getAttributeCode() == 'price')
            {
                $bIsBasePrice = true;
    #            $tableAlias    = 'price_table_'.$attribute->getAttributeCode();
                $tableName = Mage::getSingleton('core/resource')->getTableName('catalogindex/price');
                $valueAlias = 'min_price';
				$valueAlias2 = 'max_price';
            }
            else
            {
                $bIsBasePrice = false;
                $tableName = Mage::getSingleton('core/resource')->getTableName('catalog/product_index_eav_decimal');
                $valueAlias = 'value';
            }

            $collection   = $this->getLayer()->getProductCollection();
            $websiteId    = Mage::app()->getStore()->getWebsiteId();
            $custGroupId  = Mage::getSingleton('customer/session')->getCustomerGroupId();

            /**
             * Distinct required for removing duplicates in case when we have grouped products
             * which contain multiple rows for one product id
             */

            $collection->getSelect()->distinct(true);

            $sOnStatement = $tableAlias . '.entity_id=e.entity_id';

            if (!$bIsBasePrice)
            {
                $sOnStatement .= ' AND ' . $tableAlias . '.attribute_id = ' .$attribute->getId() . ' AND ' . $tableAlias . '.store_id = ' . Mage::app()->getStore()->getId();
            }

            $collection->getSelect()->joinLeft(
                array($tableAlias => $tableName), // modified line
                $sOnStatement,
                array()
            );

            $response = new Varien_Object();
            $response->setAdditionalCalculations(array());

            if ($bIsBasePrice)
            {
                $collection->getSelect()
                    ->where($tableAlias . '.website_id = ?', $websiteId)   // modified line
                    ;
            }

            if ($attribute->getAttributeCode() == 'price') {
                $collection->getSelect()->where($tableAlias . '.customer_group_id = ?', $custGroupId); // modified line
                $args = array(
                    'select'         => $collection->getSelect(),
                    'table'          => $tableAlias,
                    'store_id'       => Mage::app()->getStore()->getId(), // modified line
                    'response_object'=> $response,
                );
                Mage::dispatchEvent('catalogindex_prepare_price_select', $args);
            }

            $rate =  $this->_getCurrencyRate();

            // make query a little bit faster
            //if ($from > 0.01){
				//including max price
                //$collection->getSelect()->where("((({$tableAlias}.{$valueAlias}".implode('', $response->getAdditionalCalculations()).")*$rate) >= $from AND (({$tableAlias}.{$valueAlias}".implode('', $response->getAdditionalCalculations()).")*$rate) <= $to ) OR ((({$tableAlias}.{$valueAlias2}".implode('', $response->getAdditionalCalculations()).")*$rate) >= $from AND (({$tableAlias}.{$valueAlias2}".implode('', $response->getAdditionalCalculations()).")*$rate) <= $to) OR ((({$tableAlias}.{$valueAlias}".implode('', $response->getAdditionalCalculations()).")*$rate) <= $from AND (({$tableAlias}.{$valueAlias2}".implode('', $response->getAdditionalCalculations()).")*$rate) >= $to)");
				
				//excluding max price
				$collection->getSelect()->where("((({$tableAlias}.{$valueAlias}".implode('', $response->getAdditionalCalculations()).")*$rate) >= $from AND (({$tableAlias}.{$valueAlias}".implode('', $response->getAdditionalCalculations()).")*$rate) <= $to )");
			//}
            //if ($to > 0.01){
             //   $collection->getSelect()->where("(({$tableAlias}.{$valueAlias}".implode('', $response->getAdditionalCalculations()).")*$rate) <= ?", $to);
			//}

            $this->_isTableJoined = true;
    			//echo($collection->getSelect()->__toString()); exit();
            return $this;
        }

        protected $_isTableJoined = null;

        protected function _getBaseCollectionSql()
        {
            return $this->baseSelect;
        }

        protected function _getCurrencyRate()
        {
            $rate =  Mage::app()->getStore()->convertPrice(1000000, false);

            $rate = $rate / 1000000;

            return $rate;
        }

        public function getMaxPriceInt() /// to do - make like minmax!!
        {

            list($min,$max) = $this->getMinMaxPriceInt();

            return $max;
            /*
            $rate =  $this->_getCurrencyRate();

            $nMaxPrice = parent::getMaxPriceInt();

            // for some really UNKNOWN reason magento team
            // rounds max price to lower value :)
            return 1 + $nMaxPrice * $rate;
            */
        }

        protected function getMinMax($filter, $bIsBasePrice)
        {
            $attribute    = $this->getAttributeModel();
            $mainCollection   = $this->getLayer()->getProductCollection();
            $websiteId    = Mage::app()->getStore()->getWebsiteId();
            $custGroupId  = Mage::getSingleton('customer/session')->getCustomerGroupId();

    #        $tableAlias    = 'price_table';

            $select = clone $mainCollection->getSelect();
			//echo '<br>Query for category products->'.$select->__toString();		//	Vasim
			$select->reset(Zend_Db_Select::LIMIT_COUNT);
            $select->reset(Zend_Db_Select::LIMIT_OFFSET);

            $select->setPart(Varien_Db_Select::COLUMNS, array());
            $select->setPart(Varien_Db_Select::ORDER , array());

            $select->distinct(false);

            $tableAlias = $attribute->getAttributeCode() . '_idx';

            if ($bIsBasePrice)
            {
                $tableName = Mage::getSingleton('core/resource')->getTableName('catalogindex/price');
                $valueAlias = 'min_price';
				$valueAliasMax = 'max_price';		//	Added by Vaseem for bug 892
            }
            else
            {
                $tableName = Mage::getSingleton('core/resource')->getTableName('catalog/product_index_eav_decimal');
                $valueAlias = 'value';
            }

            $select->columns(array(
                'min_value' => new Zend_Db_Expr('MIN(' . $tableAlias . '.' . $valueAlias . ')'),
                'max_value' => new Zend_Db_Expr('MAX(' . $tableAlias . '.' . $valueAliasMax . ')'),		//	Modified by Vaseem for bug 892
            ));
			//echo '<br><br>Query for price range->'.$select->__toString();		//	Vasim
    

            if ($this->_isTableJoined)
            {
                // 1) remove from conditions
                $oldWhere = $select->getPart(Varien_Db_Select::WHERE);

                $newWhere = array();

                $alias = $tableAlias . '.' . $valueAlias;

                foreach ($oldWhere as $cond){
                   if (!strpos($cond, $alias))
                   {
                       $newWhere[] = $cond;
                    }
                }

                if ($newWhere && substr($newWhere[0], 0, 3) == 'AND')
                   $newWhere[0] = substr($newWhere[0],3);

                $select->setPart(Varien_Db_Select::WHERE, $newWhere);
            }
            else
            {
                $sOnStatement = $tableAlias . '.entity_id=e.entity_id';

                if (!$bIsBasePrice)
                {
                    $sOnStatement .= ' AND ' . $tableAlias . '.attribute_id = ' .$attribute->getId() . ' AND ' . $tableAlias . '.store_id = ' . Mage::app()->getStore()->getId();
                }
                else
                {
    #                $sOnStatement .= ' AND ' . $tableAlias . '.website_id = ' . $websiteId . ' AND ' . $tableAlias . '.customer_group_id = ' . Mage::app()->getStore()->getId();
                }

                $select->joinLeft(
                    array($tableAlias => $tableName), // modified line
                    $sOnStatement,
                    array()
                );

                $response = new Varien_Object();
                $response->setAdditionalCalculations(array());

                if ($bIsBasePrice)
                {
                    $select
                        ->where($tableAlias . '.website_id = ?', $websiteId)   // modified line
                    ;
                }

                if ($bIsBasePrice)
                {
                    $select->where($tableAlias . '.customer_group_id = ?', $custGroupId); // modified line
                    $args = array(
                        'select'         => $select,
                        'table'          => $tableAlias,
                        'store_id'       => Mage::app()->getStore()->getId(), // modified line
                        'response_object'=> $response,
                    );
                    Mage::dispatchEvent('catalogindex_prepare_price_select', $args);
                }
            }

            $adapter = Mage::getSingleton('core/resource')->getConnection('core_read');
            $result     = $adapter->fetchRow($select);
			//echo '<br><br>Query for price range->'.$select->__toString();		//	Vasim
            $rate =  $this->_getCurrencyRate();

            return array($result['min_value'] * $rate, ceil($result['max_value'] * $rate));
        }



        public function getMinMaxPriceInt()
        {
            $attribute    = $this->getAttributeModel();

            if ($attribute->getAttributeCode() == 'price')
            {
                list($min, $max) = $this->getMinMax($this, true);
            }
            else
            {
                list($min, $max) = $this->getMinMax($this, false);
            }

            $max = floor($max);
            $min = floor($min);

            return array($min, $max);
        }

        // functions for additional prices (decimal in magento 1.4)

        /**
         * Retrieve data for build decimal filter items
         *
         * @return array
         */
        protected function _getDecimalItemsData()
        {
            $key = $this->_getDecimalCacheKey();

            $data = $this->getLayer()->getAggregator()->getCacheData($key);

            if ($data === null) {
                $data       = array();
                $range      = $this->getRange();
                $dbRanges   = $this->getDecimalRangeItemCounts($range);

                foreach ($dbRanges as $index => $count) {
                    $data[] = array(
                        'label' => $this->_renderItemLabel($range, $index),
                        'value' => $index . ',' . $range,
                        'count' => $count,
                    );
                }


            }
            return $data;
        }

        const MIN_RANGE_POWER = 10;

        /**
         * Resource instance
         *
         * @var Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Decimal
         */
        protected $_resource;

        /**
         * Initialize filter and define request variable
         *
         */
        /*
        public function __construct()
        {
            parent::__construct();
            $this->_requestVar = 'decimal';
        }
    */
        /**
         * Retrieve resource instance
         *
         * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Decimal
         */
        protected function _getResource()
        {
            if (is_null($this->_resource)) {
                if ($this->getAttributeModel()->getAttributeCode() == 'price')
                {
                    $this->_resource = Mage::getResourceModel('catalog/layer_filter_price');
                }
                else
                {
                    $this->_resource = Mage::getResourceModel('catalog/layer_filter_decimal');
                }
            }
            return $this->_resource;
        }

        /**
         * Apply decimal range filter to product collection
         *
         * @param Zend_Controller_Request_Abstract $request
         * @param Mage_Catalog_Block_Layer_Filter_Decimal $filterBlock
         * @return Mage_Catalog_Model_Layer_Filter_Decimal
         */

        public function _______apply(Zend_Controller_Request_Abstract $request, $filterBlock)
        {
            parent::apply($request, $filterBlock);

            /**
             * Filter must be string: $index, $range
             */
            $filter = $request->getParam($this->getRequestVar());
            if (!$filter) {
                return $this;
            }

            $filter = explode(',', $filter);
            if (count($filter) != 2) {
                return $this;
            }

            list($index, $range) = $filter;
            if ((int)$index && (int)$range) {
                $this->setRange((int)$range);

                $this->_getResource()->applyFilterToCollection($this, $range, $index);
                $this->getLayer()->getState()->addFilter(
                    $this->_createItem($this->_renderItemLabel($range, $index), $filter)
                );

                $this->_items = array();
            }

            return $this;
        }

        /**
         * Retrieve price aggreagation data cache key
         *
         * @return string
         */
        protected function _getDecimalCacheKey()
        {
            $key = $this->getLayer()->getStateKey()
                . '_ATTR_' . $this->getAttributeModel()->getAttributeCode();
            return $key;
        }

        /**
         * Prepare text of item label
         *
         * @param   int $range
         * @param   float $value
         * @return  string
         */
        protected function _renderItemLabel($range, $value)
        {
            $from   = Mage::app()->getStore()->formatPrice(($value - 1) * $range, false);
            $to     = Mage::app()->getStore()->formatPrice($value * $range, false);
            return Mage::helper('catalog')->__('%s - %s', $from, $to);
        }

        /**
         * Retrieve maximum value from layer products set
         *
         * @return float
         */
        public function getMaxValue()
        {
            $max = $this->getData('max_value');
            if (is_null($max)) {
    //            list($min, $max) = $this->_getResource()->getMinMax($this);
                list($min, $max) = $this->getMinMax($this, false);

                $this->setData('max_value', $max);
                $this->setData('min_value', $min);
            }
            return $max;
        }

        /**
         * Retrieve minimal value from layer products set
         *
         * @return float
         */
        public function getMinValue()
        {
            $min = $this->getData('min_value');
            if (is_null($min)) {
                list($min, $max) = $this->_getResource()->getMinMax($this);
                $this->setData('max_value', $max);
                $this->setData('min_value', $min);
            }
            return $min;
        }

        /**
         * Retrieve range for building filter steps
         *
         * @return int
         */
        public function getRange()
        {
            $range = $this->getData('range');
            if (is_null($range)) {
                $max = $this->getMaxValue();
                $index = 1;
                do {
                    $range = pow(10, (strlen(floor($max)) - $index));
                    $items = $this->getDecimalRangeItemCounts($range);
                    $index ++;
                }
                while($range > self::MIN_RANGE_POWER && count($items) < 2);

                $this->setData('range', $range);
            }
            return $range;
        }

        /**
         * Retrieve information about products count in range
         *
         * @param int $range
         * @return int
         */
        public function getDecimalRangeItemCounts($range)
        {
            $rangeKey = 'range_item_counts_' . $range;
            $items = $this->getData($rangeKey);
            if (is_null($items)) {
    #            $items = $this->_getResource()->getCount($this, $range);
                $items = $this->_getResourceCount($this, $range);
                $this->setData($rangeKey, $items);
            }
            return $items;
        }

        protected function _getResourceCount($filter, $range)
        {
            $select     = $this->_getDecimalSelect($filter);
            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
    #        $connection = $this->_getResource()->_getReadAdapter();
    #        $response   = $this->_getResource()->_dispatchPreparePriceEvent($filter, $select);

    #        $table = $this->_getResource()->_getIndexTableAlias();

            $table = 'decimal_index';

    #        $additional = join('', $response->getAdditionalCalculations());

            $additional = '';

            $rate       = $filter->getCurrencyRate();
            $countExpr  = new Zend_Db_Expr("COUNT(*)");
            $rangeExpr  = new Zend_Db_Expr("FLOOR((({$table}.value {$additional}) * {$rate}) / {$range}) + 1");

            $select->columns(array(
                'range' => $rangeExpr,
                'count' => $countExpr
            ));
            $select->where("{$table}.value > 0");
            $select->group('range');
    #d($select->__toString(), 0);
            return $connection->fetchPairs($select);
        }

        protected function _getDecimalSelect($filter)
        {
            $collection = $filter->getLayer()->getProductCollection();

            // clone select from collection with filters
            $select = clone $collection->getSelect();
            // reset columns, order and limitation conditions
            $select->reset(Zend_Db_Select::COLUMNS);
            $select->reset(Zend_Db_Select::ORDER);
            $select->reset(Zend_Db_Select::LIMIT_COUNT);
            $select->reset(Zend_Db_Select::LIMIT_OFFSET);

            $attributeId = $filter->getAttributeModel()->getId();
            $storeId     = $collection->getStoreId();

            $select->join(
                array('decimal_index' => $this->_getResource()->getMainTable()),
                "e.entity_id=decimal_index.entity_id AND decimal_index.attribute_id={$attributeId}"
                    . " AND decimal_index.store_id={$storeId}",
                array()
            );

            return $select;
        }

    }
