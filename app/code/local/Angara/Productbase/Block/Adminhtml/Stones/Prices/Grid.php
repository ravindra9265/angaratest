<?php
class Angara_Productbase_Block_Adminhtml_Stones_Prices_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('pricesGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        //$this->setSaveParametersInSession(true);
        //$this->setUseAjax(true);

    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('productbase/stone_price')->getCollection();
			$collection->joinField('title',
                'productbase/stone_name',
                'title',
                'stone_id=id','','left');
				
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            
        }
        return parent::_addColumnFilterToCollection($column);
    }
	
    protected function _prepareColumns()
    {
        $this->addColumn('id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'index' => 'id'
        ));
        $this->addColumn('stone',
            array(
                'header'=> Mage::helper('catalog')->__('Stone'),
                'index' => 'stone',
        ));
		$this->addColumn('price_per_carat',
            array(
                'header'=> 'Price Per Carat',
                'index' => 'price_per_carat',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit'
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('item');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('catalog')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => "Are you sure?"
        ));

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id'=>$row->getId())
        );
    }
}
