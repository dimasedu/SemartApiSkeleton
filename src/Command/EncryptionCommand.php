<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\Command;

use KejawenLab\ApiSkeleton\Util\Encryptor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 */
final class EncryptionCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('semart:encrypt')
            ->setDescription('Encrypt Plain Text')
            ->addArgument('text', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('<info>%s</info>', Encryptor::encrypt($input->getArgument('text'), $_SERVER['APP_SECRET'])));

        return 0;
    }
}
