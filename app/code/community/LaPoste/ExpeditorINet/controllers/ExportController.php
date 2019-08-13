<?php
/**
 * LaPoste_ExpeditorINet
 *
 * @category    LaPoste
 * @package     LaPoste_ExpeditorINet
 * @copyright   Copyright (c) 2010 La Poste
 * @author 	    Smile (http://www.smile.fr) & Jibé
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class LaPoste_ExpeditorINet_ExportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setUsedModuleName('LaPoste_ExpeditorINet');
    }

    /**
     * Check whether the admin user has access to this controller.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/expeditorinet/export');
    }

    /**
     * Main action : show orders list
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/expeditorinet/export')
            ->_addContent($this->getLayout()->createBlock('expeditorinet/export_orders'))
            ->renderLayout();
    }

    /**
     * Convert civility in letters to a code for Expeditor
     *
     * @param string $civility a civility
     * @return int
     */
    protected function _getExpeditorCodeForCivility($civility)
    {
        if ($civility === 'MR') {
            return 2;
        } elseif ($civility === 'MME') {
            return 3;
        } elseif ($civility === 'MLE') {
            return 4;
        } else {
            return 1;
        }
    }

    /**
     * Export Action
     * Generates a CSV file to download
     *
     * @return void
     */
    public function exportAction()
    {
        // get the orders
        $orderIds = $this->getRequest()->getPost('order_ids');

        // get configuration
        $separator = Mage::helper('expeditorinet')->getConfigurationFieldSeparator();
        $delimiter = Mage::helper('expeditorinet')->getConfigurationFieldDelimiter();
        if ($delimiter === 'simple_quote') {
            $delimiter = "'";
        } elseif ($delimiter === 'double_quotes') {
            $delimiter = '"';
        }
        $lineBreak = Mage::helper('expeditorinet')->getConfigurationEndOfLineCharacter();
        if ($lineBreak === 'lf') {
            $lineBreak = "\n";
        } elseif ($lineBreak === 'cr') {
            $lineBreak = "\r";
        } elseif ($lineBreak === 'crlf') {
            $lineBreak = "\r\n";
        }
        $fileExtension = Mage::helper('expeditorinet')->getConfigurationFileExtension();
        $fileCharset = Mage::helper('expeditorinet')->getConfigurationFileCharset();

        // So Colissimo product codes for Hors Domicile
        $hdProductCodes = Mage::helper('expeditorinet')->getPickupPointCodes();

        // set the filename
        $filename   = 'orders_export_'.Mage::getSingleton('core/date')->date('Ymd_His') . $fileExtension;

        // get company commercial name
        $commercialName = Mage::helper('expeditorinet')->getCompanyCommercialName();

        // initialize the content variable
        $content = '';

        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                // get the order
                $order = Mage::getModel('sales/order')->load($orderId);

                // if the product code is for Hors Domicile we should take the billing address
                if (in_array($order->getSocoProductCode(), $hdProductCodes)) {
                    // get the shipping address
                    $address = $order->getBillingAddress();
                } else {
                    // get the billing address
                    $address = $order->getShippingAddress();
                }

                // real order id
                $content = $this->_addFieldToCsv($content, $delimiter, $order->getRealOrderId());
                $content .= $separator;
                // customer first name
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getFirstname());
                $content .= $separator;
                // customer last name
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getLastname());
                $content .= $separator;
                // customer company
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getCompany());
                $content .= $separator;
                // street address, on 4 fields
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getStreet(1));
                $content .= $separator;
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getStreet(2));
                $content .= $separator;
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getStreet(3));
                $content .= $separator;
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getStreet(4));
                $content .= $separator;
                // postal code
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getPostcode());
                $content .= $separator;
                // city
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getCity());
                $content .= $separator;
                // country code
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getCountry());
                $content .= $separator;
                // portable phone number
                $telephone = '';
                if ($order->getSocoPhoneNumber() != '' && $order->getSocoPhoneNumber() != null) {
                    $telephone = $order->getSocoPhoneNumber();
                } elseif ($address->getTelephone() != '' && $address->getTelephone() != null) {
                    $telephone = $address->getTelephone();
                }
                $content = $this->_addFieldToCsv($content, $delimiter, $telephone);
                $content .= $separator;
                // product code
                $content = $this->_addFieldToCsv($content, $delimiter, $this->_getProductCode($order));
                $content .= $separator;
                // shipping instruction
                $content = $this->_addFieldToCsv($content, $delimiter, $order->getSocoShippingInstruction());
                $content .= $separator;
                // civility
                $civility = $this->_getExpeditorCodeForCivility($order->getSocoCivility());
                $content = $this->_addFieldToCsv($content, $delimiter, $civility);
                $content .= $separator;
                // door code 1
                $content = $this->_addFieldToCsv($content, $delimiter, $order->getSocoDoorCode1());
                $content .= $separator;
                // door code 2
                $content = $this->_addFieldToCsv($content, $delimiter, $order->getSocoDoorCode2());
                $content .= $separator;
                // interphone
                $content = $this->_addFieldToCsv($content, $delimiter, $order->getSocoInterphone());
                $content .= $separator;
                // relay point code
                $content = $this->_addFieldToCsv($content, $delimiter, $order->getSocoRelayPointCode());
                $content .= $separator;
                // network code
                $content = $this->_addFieldToCsv($content, $delimiter, $order->getSocoNetworkCode());
                $content .= $separator;
                // email
                $content = $this->_addFieldToCsv($content, $delimiter, $order->getSocoEmail());
                $content .= $separator;

                // total weight
                $total_weight = 0;
                $items = $order->getAllItems();
                foreach ($items as $item) {
                    $total_weight += $item['row_weight'];
                }
                $content = $this->_addFieldToCsv($content, $delimiter, $total_weight);
                $content .= $separator;

                // company commercial name
                $content = $this->_addFieldToCsv($content, $delimiter, $commercialName);

                $content .= $lineBreak;
            }

            // decode the content, depending on the charset
            if ($fileCharset == 'ISO-8859-1') {
                $content = utf8_decode($content);
            }

            // pick file mime type, depending on the extension
            if ($fileExtension == '.txt') {
                $fileMimeType = 'text/plain';
            } elseif ($fileExtension == '.csv') {
                $fileMimeType = 'application/csv';
            } else {
                // default
                $fileMimeType = 'text/plain';
            }

            // download the file
            return $this->_prepareDownloadResponse($filename, $content, $fileMimeType .'; charset="'. $fileCharset .'"');
        } else {
            $this->_getSession()->addError($this->__('No Order has been selected'));
        }
    }

    /**
     * Add a new field to the csv file
     *
     * @param csvContent     the current csv content
     * @param fieldDelimiter the delimiter character
     * @param fieldContent   the content to add
     * @return string
     */
    protected function _addFieldToCsv($csvContent, $fieldDelimiter, $fieldContent)
    {
        return $csvContent . $fieldDelimiter . $fieldContent . $fieldDelimiter;
    }

    /**
     * Get the So Colissimo product code stored in the order data.
     *
     * @param Mage_Sales_Model_Order $order the order
     * @return string
     */
    protected function _getProductCode($order)
    {
        $value = $order->getSocoProductCode();

        // request a signature for home delivery
        if ($value === 'DOM' && Mage::helper('expeditorinet')->getConfigurationSignatureRequired()) {
            $value = 'DOS';
        }

        return $value;
    }
}
