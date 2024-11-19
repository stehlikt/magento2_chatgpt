<?php

namespace Railsformers\ChatGPT\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ChatgptCron extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('chatgpt_cron','cron_id');
    }
}
