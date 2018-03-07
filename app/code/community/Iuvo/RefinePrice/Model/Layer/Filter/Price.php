<?php
class Iuvo_RefinePrice_Model_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getPriceRange()
    {
        $range = $this->getData('price_range');
        if (!$range) {
            $currentCategory = Mage::registry('current_category_filter');
            if ($currentCategory) {
                $range = $currentCategory->getFilterPriceRange();
            } else {
                $range = $this->getLayer()->getCurrentCategory()->getFilterPriceRange();
            }

            $maxPrice = $this->getMaxPriceInt();
            if (!$range) {
                $calculation = Mage::app()->getStore()->getConfig(self::XML_PATH_RANGE_CALCULATION);
                if ($calculation == self::RANGE_CALCULATION_AUTO) {
                    $index = 1;
                    do {
                        $range = pow(2, (strlen(floor($maxPrice)) - $index));
                        $items = $this->getRangeItemCounts($range);
                        $index++;
                    }
                    while($range > self::MIN_RANGE_POWER && count($items) < 2);
                } else {
                    $range = Mage::app()->getStore()->getConfig(self::XML_PATH_RANGE_STEP);
                }
            }

            while (ceil($maxPrice / $range) > 25) {
                $range *= 10;
            }

            $this->setData('price_range', $range);
        }

        return $range;
    }
}
