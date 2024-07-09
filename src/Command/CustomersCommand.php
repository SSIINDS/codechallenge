<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use App\Service\CustomersService;

#[AsCommand(
    name: 'cron:fetch-customers',
    description: 'Add a short description for your command',
)]
class CustomersCommand extends Command
{
    public function __construct(
        private CustomersService $CustomersService
    ){
        parent::__construct();
    }



    protected function configure(): void
    {
        $this
            ->addOption('count',null, InputArgument::OPTIONAL, 'Number of Customers?',100)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $count = $input->getOption('count')?? 100;
        $result = $this->CustomersService->fetch($count);
        if($result['status']){
            $io->success($result['message']);
        } else {
            $io->error($result['message']);
        }
        return Command::SUCCESS;
    }
}
