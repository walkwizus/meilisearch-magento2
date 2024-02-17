<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Walkwizus\MeilisearchCatalog\Model\ResourceModel\Indexer\Category\Action\Full;

class TestCommand extends Command
{
    public function __construct(
        protected Full $full
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('ms:test');
        $this->setDescription('MS Test');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->full->getCategories(1);

        return 0;
    }
}
