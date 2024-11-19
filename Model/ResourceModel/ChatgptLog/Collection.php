<?php

namespace Railsformers\ChatGPT\Model\ResourceModel\ChatgptLog;

use Railsformers\ChatGPT\Model\ResourceModel\ChatgptLog AS ChatgptLogRessource;
use Railsformers\ChatGPT\Model\ChatgptLog AS ChatgptLogModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            ChatgptLogModel::class,
            ChatgptLogRessource::class
        );
    }
}
