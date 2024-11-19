<?php

namespace Railsformers\ChatGPT\Console\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\ObjectManagerInterface;

class ClearLog extends Command
{

    const NAME_ARGUMENT = "name";
    protected $objectMannager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectMannager = $objectManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('chatgpt:clear-log');
        $this->setDescription('Clearing ChatGPT logs');
        /*$this->setDefinition([
            new InputArgument(self::NAME_ARGUMENT, InputArgument::OPTIONAL, "Name")
        ]);*/
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $log_model = $this->objectMannager->create('Railsformers\ChatGPT\Model\ChatgptLog');

        $logs = $log_model->getCollection()->addFieldToFilter('created_at', ['lt' => date('Y-m-d H:i:s', strtotime('-3 months'))]);

        $output->writeln('Deleting chatGPT logs...');
        foreach ($logs as $log)
        {
            $log->delete();
        }

        $output->writeln('Done!');
    }
}
