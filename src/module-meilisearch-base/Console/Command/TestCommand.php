<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Walkwizus\MeilisearchMerchandising\Model\AttributeRuleProvider;

class TestCommand extends Command
{
    public function __construct(
        protected AttributeRuleProvider $attributeRuleProvider
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
        $this->attributeRuleProvider->getAttributes();

        return 0;
    }
}
