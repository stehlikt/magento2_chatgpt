<?php

namespace Railsformers\ChatGPT\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\ObjectManagerInterface;

class GenerateContent extends Command
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
        $this->setName('chatgpt:generate-content');
        $this->setDescription('Generating content with ChatGPT');
        /*$this->setDefinition([
            new InputArgument(self::NAME_ARGUMENT, InputArgument::OPTIONAL, "Name")
        ]);*/
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $cron_model = $this->objectMannager->create('Railsformers\ChatGPT\Model\ChatgptCron');

        $urls = $cron_model->getCollection();
        foreach ($urls as $url)
        {
            $output->writeln('Running URL: '. $url->getData('cron_url'));
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url->getData('cron_url'));
            curl_setopt($ch, CURLOPT_HEADER, 0);

            curl_exec($ch);

            curl_close($ch);

            $url->delete();
        }
        //$type = $input->getArgument(self::NAME_ARGUMENT);

        $output->writeln('Done!');
    }
}
