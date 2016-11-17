<?php
class Turiknox_Cachebuster_Block_Page_Html_Head extends Mage_Page_Block_Html_Head
{
    /**
     * Merge static and skin files of the same format into 1 set of HEAD directives or even into 1 directive
     *
     * Will attempt to merge into 1 directive, if merging callback is provided. In this case it will generate
     * filenames, rather than render urls.
     * The merger callback is responsible for checking whether files exist, merging them and giving result URL
     *
     * @param string $format - HTML element format for sprintf('<element src="%s"%s />', $src, $params)
     * @param array $staticItems - array of relative names of static items to be grabbed from js/ folder
     * @param array $skinItems - array of relative names of skin items to be found in skins according to design config
     * @param callback $mergeCallback
     * @return string
     */
    protected function &_prepareStaticAndSkinElements($format, array $staticItems, array $skinItems,
                                                      $mergeCallback = null)
    {
        $designPackage = Mage::getDesign();
        $baseJsUrl = Mage::getBaseUrl('js');
        $items = array();
        if ($mergeCallback && !is_callable($mergeCallback)) {
            $mergeCallback = null;
        }

        // get static files from the js folder, no need in lookups
        foreach ($staticItems as $params => $rows) {
            foreach ($rows as $name) {
                $items[$params][] = $mergeCallback ? Mage::getBaseDir() . DS . 'js' . DS . $name : $baseJsUrl . $name;
            }
        }

        // lookup each file basing on current theme configuration
        foreach ($skinItems as $params => $rows) {
            foreach ($rows as $name) {
                $items[$params][] = $mergeCallback ? $designPackage->getFilename($name, array('_type' => 'skin'))
                    : $designPackage->getSkinUrl($name, array());
            }
        }

        $html = '';
        foreach ($items as $params => $rows) {
            // attempt to merge
            $mergedUrl = false;
            if ($mergeCallback) {
                $mergedUrl = call_user_func($mergeCallback, $rows);
            }
            // render elements
            $params = trim($params);
            $params = $params ? ' ' . $params : '';
            if ($mergedUrl) {
                $mergedUrl .= $this->prepareResourceSuffix($mergedUrl);
                $html .= sprintf($format, $mergedUrl, $params);
            } else {
                foreach ($rows as $src) {
                    $src .= $this->prepareResourceSuffix($src);
                    $html .= sprintf($format, $src, $params);
                }
            }
        }
        return $html;
    }

    /**
     * Add suffix to resource URL
     *
     * @param $file
     * @return string
     */
    public function prepareResourceSuffix($file)
    {
        if ($this->helper('turiknox_cachebuster')->isCacheBusterEnabled()) {
            if (!$this->helper('turiknox_cachebuster')->isUseCustomSuffix()) {
                $file = $this->replaceBaseUrlWithDir($file);
                if (file_exists($file)) {
                    $suffix = filemtime($file);
                } else {
                    return false;
                }
            } else {
                $suffix = $this->helper('turiknox_cachebuster')->getCustomSuffix();
            }
            return sprintf('?v=%s', urlencode($suffix));
        }
        return false;
    }

    /**
     * Replace base URL with base dir
     *
     * @param $file
     * @return string
     */
    public function replaceBaseUrlWithDir($file)
    {
        return str_replace(Mage::getBaseUrl(), Mage::getBaseDir() . DS, $file);
    }
}