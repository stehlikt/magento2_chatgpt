<?php

namespace Railsformers\ChatGPT\Setup;

use Magento\Backend\Block\Widget\Tab;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $table_name =  $installer->getTable('chatgpt_log');

        if($installer->getConnection()->isTableExists($table_name) != true)
        {
            $table = $installer->getConnection()
                ->newTable($table_name)
                ->addColumn(
                    'log_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Log ID'
                )
                ->addColumn(
                    'log_text',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Log Text'
                )
                ->addColumn(
                    'tokens',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Tokens'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Created At'
                );
            $installer->getConnection()->createTable($table);
            $installer->endSetup();
        }

        $installer = $setup;
        $installer->startSetup();
        $table_name = $installer->getTable('chatgpt_cron');
        if($installer->getConnection()->isTableExists($table_name) != true)
        {
            $table = $installer->getConnection()
                ->newTable($table_name)
                ->addColumn(
                    'cron_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Cron id'
                )
                ->addColumn(
                    'cron_url',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Cron URL'
                )
                ->addColumn(
                    'cron_ids',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Cron IDs'
                )
                ->addColumn(
                    'cron_type',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Cron type'
                );

            $installer->getConnection()->createTable($table);
            $installer->endSetup();
        }
    }
}
