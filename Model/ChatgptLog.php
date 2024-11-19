<?php

namespace Railsformers\ChatGPT\Model;

use Magento\Framework\Model\AbstractModel;

class ChatgptLog extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Railsformers\ChatGPT\Model\ResourceModel\ChatgptLog::class);
    }
}
