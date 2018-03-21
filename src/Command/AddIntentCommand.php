<?php

namespace App\Command;

use App\Entity\Intent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddIntentCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'app:add-intent';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('name', InputArgument::REQUIRED, 'The intent name')
            ->addArgument('parameters', InputArgument::IS_ARRAY, 'An array of parameters');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');
        $parameters = $input->getArgument('parameters');

        $intent = new Intent();
        $intent->setName($name)->setParameters($parameters);

        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $manager->persist($intent);
        $manager->flush();


        $io->success(sprintf('You have a successfully added %s', $intent->getName()));
    }
}
