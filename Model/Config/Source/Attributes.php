<?php

namespace Railsformers\ChatGPT\Model\Config\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Attributes implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $attributes = [];
        $attributeCollection = $this->collectionFactory->create();
        foreach ($attributeCollection->getItems() as $attribute) {
            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }
        return $attributes;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $arr = $this->toArray();
        foreach ($arr as $value => $label) {
            $optionArray[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $optionArray;
    }
}
