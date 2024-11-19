<?php

namespace Railsformers\ChatGPT\Block\Adminhtml\Category\Edit;

use Railsformers\ChatGPT\Helper\Data;
class SeoDescriptionButton extends \Magento\Backend\Block\Template{

    protected $_template = 'Railsformers_ChatGPT::seo_description_button.phtml';

    protected $helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }
    public function getButtonHtml()
    {
        $isEnable = $this->helper->isEnabled();
        if($this->_request->getParam('id') && $isEnable)
        {
            $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData(['class' => 'seo_button','id' => 'meta_description', 'label' => __('Generovat obsah pomocí ChatGPT')]);
            return $button->toHtml();
        }

    }
}
