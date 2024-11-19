<?php

namespace Railsformers\ChatGPT\Block\Adminhtml\Category;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

class GenerateContent extends Template
{

    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context,$data);
    }

    public function GenerateCronUrl()
    {
        $params = ['saveUrl' => true,'type' => 'types_str','_query' => ['form_key' => $this->formKey->getFormKey()]];
        return $this->getUrl('chatgpt/index/generatecategories',$params);
    }
}
