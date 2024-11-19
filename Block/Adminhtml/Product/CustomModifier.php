<?php

namespace Railsformers\ChatGPT\Block\Adminhtml\Product;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Container;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;
use Railsformers\ChatGPT\Helper\Data;

class CustomModifier extends AbstractModifier
{
    protected $arrayManager;

    protected $meta = [];

    protected $scopeName;

    protected $urlBuilder;

    protected $request;

    protected $helper;

    public function __construct(ArrayManager $arrayManager, UrlInterface $urlBuilder, RequestInterface $request, Data $helper, $scopeName = '')
    {
        $this->arrayManager = $arrayManager;
        $this->scopeName = $scopeName;
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->helper = $helper;
    }
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        $isEnable = $this->helper->isEnabled();
        if ($this->request->getParam('id') && $isEnable) {
            $this->customSeoDescriptionButton();
            $this->customSeoTitleButton();
        }


        return $this->meta;
    }

    private function customSeoDescriptionButton()
    {

        $pricePath = $this->arrayManager->findPath(
            ProductAttributeInterface::CODE_SEO_FIELD_META_DESCRIPTION,
            $this->meta,
            null,
            'children'
        );

        if ($pricePath) {

            $advancedPricingButton['arguments']['data']['config'] = [
                'dataScope' => 'generate_seo_description_button',
                'displayAsLink' => true,
                'ignoreButtonClass' => true,
                'formElement' => Container::NAME,
                'componentType' => Container::NAME,
                'component' => 'Magento_Ui/js/form/components/button',
                'template' => 'ui/form/components/button/container',
                'title' => __('Generovat obsah pomocí ChatGPT'),
                'label' => ' ',
                'additionalClasses' => 'generate_seo_button',
                'additionalForGroup' => true,
                'provider' => false,
                'visible' => 1,
                'source' => 'product_details',
                'sortOrder' =>
                $this->arrayManager->get($pricePath . '/arguments/data/config/sortOrder', $this->meta) - 1,
            ];

            $this->meta = $this->arrayManager->set($this->arrayManager->slicePath($pricePath, 0, -1) . '/generate_seo_description_button', $this->meta, $advancedPricingButton);
        }

        return $this;
    }

    private function customSeoTitleButton()
    {

        $pricePath = $this->arrayManager->findPath(
            ProductAttributeInterface::CODE_SEO_FIELD_META_TITLE,
            $this->meta,
            null,
            'children'
        );

        if ($pricePath) {

            $advancedPricingButton['arguments']['data']['config'] = [
                'dataScope' => 'generate_seo_title_button',
                'displayAsLink' => true,
                'ignoreButtonClass' => true,
                'formElement' => Container::NAME,
                'componentType' => Container::NAME,
                'component' => 'Magento_Ui/js/form/components/button',
                'template' => 'ui/form/components/button/container',
                'title' => __('Generovat obsah pomocí ChatGPT'),
                'label' => ' ',
                'additionalClasses' => 'generate_seo_button',
                'additionalForGroup' => true,
                'provider' => false,
                'source' => 'product_details',
                'sortOrder' =>
                $this->arrayManager->get($pricePath . '/arguments/data/config/sortOrder', $this->meta) - 1,
            ];

            $this->meta = $this->arrayManager->set($this->arrayManager->slicePath($pricePath, 0, -1) . '/generate_seo_title_button', $this->meta, $advancedPricingButton);
        }

        return $this;
    }

    public function modifyData(array $data)
    {
        return $data;
    }
}
