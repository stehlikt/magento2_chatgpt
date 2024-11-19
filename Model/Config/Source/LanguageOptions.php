<?php

namespace Railsformers\ChatGPT\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Locale\ListsInterface;

class LanguageOptions implements OptionSourceInterface{

    protected $languageOptions;

    public function __construct(ListsInterface $languageOptions)
    {
        $this->languageOptions = $languageOptions;
    }

    public function toOptionArray()
    {
        $langs = $this->languageOptions->getOptionLocales();
        $optionArray = [];

        foreach($langs as $lang)
        {
            $optionArray[] =
                [
                    'label' => $lang['label'],
                    'value' => $lang['value']
                ];
        }

        return $optionArray;
    }
}
