<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Block_Adminhtml_Customer_Edit_Tab_Expercash_Scoring
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function _construct()
    {
        parent::_construct();
        $this->setId('customer_edit_tab_expercash_scoring');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }


    protected function _prepareLayout()
    {
        $customer = Mage::registry('current_customer');
        $url      = $this->getUrl(
            'scoring/adminhtml_scoring/delete',
            array('_current' => true, 'customer_id' => $customer->getId())
        );
        $this->setChild(
            'delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                         'label'   => Mage::helper('expercash_scoring/data')
                                 ->__(
                                     'Delete scoring information'
                                 ),
                         'onclick' => "setLocation('$url')",
                         'class'   => 'task'
                    )
                )
        );
        parent::_prepareLayout();
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }


    public function getMainButtonsHtml()
    {
        $html = '';
        $html .= $this->getDeleteButtonHtml();
        if ($this->getFilterVisibility()) {
            $html .= $this->getResetFilterButtonHtml();
            $html .= $this->getSearchButtonHtml();
        }

        return $html;
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Scoring Information');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Scoring Information');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $customer = Mage::registry('current_customer');
        if ($customer && 0 < $customer->getId()) {
            return true;
        }

        return false;
    }


    /*
     * Retrieves Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        $customer = Mage::registry('current_customer');

        return $this->getUrl(
            'scoring/adminhtml_scoring/grid', array('id' => $customer->getId())
        );
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }


    /**
     * Defines after which tab, this tab should be rendered
     *
     * @return string
     */
    public function getAfter()
    {
        return 'orders';
    }


    protected function _prepareCollection()
    {
        $customer   = Mage::registry('current_customer');
        $collection = Mage::getModel('expercash_scoring/solvency_check_result')
            ->getCollection()
            ->addFieldToFilter('customer_id', $customer->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $customer = Mage::registry('current_customer');
        $this->addColumn(
            'escore', array(
                           'header'   => Mage::helper('expercash_scoring/data')
                                   ->__('Scoring value'),
                           'align'    => 'right',
                           'index'    => 'escore',
                           'renderer' => 'expercash_scoring/adminhtml_customer_edit_tab_expercash_renderer_escore_value'
                      )
        );
        $this->addColumn(
            'escore_value', array(
                                 'header'   => Mage::helper(
                                         'expercash_scoring/data'
                                     )->__('Escore Value'),
                                 'index'    => 'escore_value',
                            )
        );
        $this->addColumn(
            'escore_feature', array(
                                   'header'   => Mage::helper(
                                           'expercash_scoring/data'
                                       )->__('Escore Feature'),
                                   'index'    => 'escore_feature',
                                   'renderer' => 'expercash_scoring/adminhtml_customer_edit_tab_expercash_renderer_escore_condition'
                              )
        );
        $this->addColumn(
            'customer_gender', array(
                                    'header' => Mage::helper(
                                            'expercash_scoring/data'
                                        )->__('Customer gender'),
                                    'index'  => 'customer_gender',
                               )
        );
        $this->addColumn(
            'customer_date_of_birth', array(
                                           'header' => Mage::helper(
                                                   'expercash_scoring/data'
                                               )->__('Customer date of birth'),
                                           'index'  => 'customer_date_of_birth',
                                      )
        );
        $this->addColumn(
            'customer_prename', array(
                                     'header'   => Mage::helper(
                                             'expercash_scoring/data'
                                         )->__('Customer prename'),
                                     'index'    => 'customer_prename',
                                     'renderer' => 'expercash_scoring/adminhtml_customer_edit_tab_expercash_renderer_customer_gender'
                                )
        );

        $this->addColumn(
            'customer_name', array(
                                  'header' => Mage::helper(
                                          'expercash_scoring/data'
                                      )->__('Customer name'),
                                  'index'  => 'customer_name',
                             )
        );

        $this->addColumn(
            'customer_address1', array(
                                      'header'   => Mage::helper(
                                              'expercash_scoring/data'
                                          )->__('Customer street'),
                                      'index'    => 'customer_address1',
                                      'renderer' => 'expercash_scoring/adminhtml_customer_edit_tab_expercash_renderer_address_street'
                                 )
        );
        $this->addColumn(
            'customer_zip', array(
                                 'header' => Mage::helper(
                                         'expercash_scoring/data'
                                     )->__('Customer zip'),
                                 'index'  => 'customer_zip',
                            )
        );
        $this->addColumn(
            'customer_city', array(
                                  'header' => Mage::helper(
                                          'expercash_scoring/data'
                                      )->__('Customer city'),
                                  'index'  => 'customer_city',
                             )
        );
        $this->addColumn(
            'customer_city', array(
                                  'header' => Mage::helper(
                                          'expercash_scoring/data'
                                      )->__('Customer city'),
                                  'index'  => 'customer_city',
                             )
        );
        $this->addColumn(
            'customer_country', array(
                                     'header'   => Mage::helper(
                                             'expercash_scoring/data'
                                         )->__('Customer country'),
                                     'index'    => 'customer_country',
                                     'renderer' => 'expercash_scoring/adminhtml_customer_edit_tab_expercash_renderer_address_country'
                                )
        );

        $this->addColumn(
            'created_at', array(
                               'header' => Mage::helper(
                                       'expercash_scoring/data'
                                   )->__('created at'),
                               'index'  => 'created_at',
                          )
        );


        $this->addColumn(
            'action',
            array(
                 'header'    => Mage::helper('expercash_scoring/data')->__(
                         'Action'
                     ),
                 'width'     => '100',
                 'type'      => 'action',
                 'getter'    => 'getId',
                 'actions'   => array(
                     array(
                         'caption' => Mage::helper('expercash_scoring/data')
                                 ->__('Delete'),
                         'url'     => array(
                             'base'   => 'scoring/adminhtml_scoring/delete',
                             'params' => array(
                                 'customer_id' =>
                                     $customer->getId()
                             )
                         ),
                         'field'   => 'check_id',
                     )
                 ),
                 'filter'    => false,
                 'sortable'  => false,
                 'index'     => 'stores',
                 'is_system' => true,
            )
        );

        return parent::_prepareColumns();
    }
} 