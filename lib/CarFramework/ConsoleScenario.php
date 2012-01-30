<?php

namespace CarFramework;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ConsoleScenario extends Command
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct("example");
        $this->em = $entityManager;
    }

    protected function configure()
    {
        $parts = explode("\\", str_replace("_", "\\", get_class($this)));
        $name = str_replace("Scenario", "", array_pop($parts));
        $group = array_pop($parts);

        $this->setName(strtolower("example:$group:$name"));
        $this->setDescription("Testcommand");
        $this->addArgument('args', InputArgument::OPTIONAL | InputArgument::IS_ARRAY);
    }

    final protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('quiet')) {
            $logger = new \CarFramework\ConsoleSQLLogger($output);
            $this->em->getConfiguration()->setSQLLogger($logger);
        }

        $this->play($this->em, $input);
    }

    /**
     * Play the scenario
     *
     * @param EntityManager $entityManager
     * @param InputInterface $input
     */
    abstract public function play(EntityManager $entityManager, InputInterface $input);
}

