<?php

namespace App\Command;

use App\Service\RoleService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:makeAdmin', description: 'Promotes user to be Admin')]
class MakeAdminCommand extends Command
{
    public function __construct(private RoleService $roleService)
    {
        parent::__construct();

    }

    protected function configure(): void
    {
        $this->addArgument('user-id',InputArgument::REQUIRED,'User ID');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = (int)$input->getArgument('user-id');

        $this->roleService->grandAdmin($userId);

        return Command::SUCCESS;
    }


}