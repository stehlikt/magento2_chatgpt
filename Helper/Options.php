<?php

namespace Railsformers\ChatGPT\Helper;

use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;

class Options implements \JsonSerializable
{
    protected $options;
    protected $data;
    protected $urlBuilder;
    protected $urlPath;
    protected $paramName;
    protected $additionalData = [];

    public function __construct(
        UrlInterface $urlBuilder,
        array $data = []
    )
    {
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
    }

    public function jsonSerialize(): mixed
    {
        if ($this->options === null) {
            $options = [[
                'value' => 'all',
                'label' => 'Všechno'
            ], [
                'value' => 'description',
                'label' => 'Popis'
            ], [
                'value' => 'shortDescription',
                'label' => 'Krátký popis'
            ], [
                'value' => 'metaTitle',
                'label' => 'Meta titulek'
            ], [
                'value' => 'metaDescription',
                'label' => 'Meta popis'
            ]];
            $this->prepareData();
            foreach ($options as $optionCode) {
                $arr = [
                    'type' =>  $optionCode['value'],
                    'label' => __($optionCode['label']),
                    '__disableTmpl' => true,
                    'url' => $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $optionCode['value']]
                    )
                ];
                foreach($this->additionalData as $key => $value)
                {
                    $arr[$key] = $value;
                }
                $this->options[] = $arr;
            }
        }
        return $this->options;
    }

    protected function prepareData()
    {
        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->urlPath = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                case 'confirm':
                    foreach ($value as $messageName => $message) {
                        $this->additionalData[$key][$messageName] = (string)new Phrase($message);
                    }
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }
}
