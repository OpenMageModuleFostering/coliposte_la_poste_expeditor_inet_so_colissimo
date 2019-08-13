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
class LaPoste_ExpeditorINet_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get export file extension
     *
     * @return string
     */
    public function getConfigurationFileExtension()
    {
        return Mage::getStoreConfig('expeditorinet/export/file_extension');
    }

    /**
     * Get export file charset
     *
     * @return string
     */
    public function getConfigurationFileCharset()
    {
        return Mage::getStoreConfig('expeditorinet/export/file_charset');
    }

    /**
     * Get export end of line character
     *
     * @return string
     */
    public function getConfigurationEndOfLineCharacter()
    {
        return Mage::getStoreConfig('expeditorinet/export/endofline_character');
    }

    /**
     * Get export field delimiter
     *
     * @return string
     */
    public function getConfigurationFieldDelimiter()
    {
        return Mage::getStoreConfig('expeditorinet/export/field_delimiter');
    }

    /**
     * Get export field separator
     *
     * @return string
     */
    public function getConfigurationFieldSeparator()
    {
        return Mage::getStoreConfig('expeditorinet/export/field_separator');
    }

    /**
     * Get export company name
     *
     * @return string
     */
    public function getCompanyCommercialName()
    {
        return Mage::getStoreConfig('expeditorinet/export/company_commercial_name');
    }

    /**
     * Get import email
     *
     * @return string
     */
    public function getConfigurationSendEmail()
    {
        return Mage::getStoreConfig('expeditorinet/import/send_email');
    }

    /**
     * Get import include comment flag
     *
     * @return string
     */
    public function getConfigurationIncludeComment()
    {
        return Mage::getStoreConfig('expeditorinet/import/include_comment');
    }

    /**
     * Get import default tracking title
     *
     * @return string
     */
    public function getConfigurationDefaultTrackingTitle()
    {
        return Mage::getStoreConfig('expeditorinet/import/default_tracking_title');
    }

    /**
     * Get import shipping comment
     *
     * @return string
     */
    public function getConfigurationShippingComment()
    {
        return Mage::getStoreConfig('expeditorinet/import/shipping_comment');
    }

    /**
     * Get import carrier code
     *
     * @return string
     */
    public function getConfigurationCarrierCode()
    {
        return Mage::getStoreConfig('expeditorinet/import/carrier_code');
    }

    /**
     * Get pickup point codes
     *
     * @return array
     */
    public function getPickupPointCodes()
    {
        $codes = Mage::getStoreConfig('expeditorinet/pickup_point_codes');

        return is_array($codes) ? array_keys($codes) : array();
    }
}
