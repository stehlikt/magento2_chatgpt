<?php

namespace Railsformers\ChatGPT\Model\ResourceModel\ChatgptCron;

use Railsformers\ChatGPT\Model\ResourceModel\ChatgptCron AS ChatgptCronRessource;
use Railsformers\ChatGPT\Model\ChatgptCron AS ChatgptCronModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            ChatgptCronModel::class,
            ChatgptCronRessource::class
        );
    }
}
