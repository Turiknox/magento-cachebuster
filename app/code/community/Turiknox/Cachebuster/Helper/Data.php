<?php
/*
 * Turiknox_Cachebuster

 * @category   Turiknox
 * @package    Turiknox_Cachebuster
 * @copyright  Copyright (c) 2017 Turiknox
 * @license    https://github.com/Turiknox/magento-cachebuster/blob/master/LICENSE.md
 * @version    1.0.1
 */
class Turiknox_Cachebuster_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Check if module is enabled
     *
     * @return boolean
     */
    public function isCacheBusterEnabled()
    {
        return Mage::getStoreConfig('dev/cachebuster/enabled');
    }

    /**
     * Check if custom suffix is enabled
     *
     * @return boolean
     */
    public function isUseCustomSuffix()
    {
        return Mage::getStoreConfig('dev/cachebuster/use_custom_suffix');
    }

    /**
     * Get the custom suffix
     *
     * @return string
     */
    public function getCustomSuffix()
    {
        return Mage::getStoreConfig('dev/cachebuster/custom_suffix');
    }
}