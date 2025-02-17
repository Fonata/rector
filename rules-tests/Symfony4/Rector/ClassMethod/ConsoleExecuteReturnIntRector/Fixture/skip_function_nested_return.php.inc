<?php

namespace Rector\Tests\Symfony4\Rector\ClassMethod\ConsoleExecuteReturnIntRector\Fixture;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SkipFunctionNestedReturn extends Command
{
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        function go() {
            return null;
        }

        return 0;
    }
}
