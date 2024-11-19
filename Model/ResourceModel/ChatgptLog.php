<?php

namespace Railsformers\ChatGPT\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ChatgptLog extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('chatgpt_log','log_id');

    }
}
