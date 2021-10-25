<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Console\Command;

use Elao\Enum\EnumInterface;
use Elao\Enum\JsDumper\JsDumper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DumpJsEnumsCommand extends Command
{
    protected static $defaultName = 'elao:enum:dump-js';

    /**
     * @var array<class-string<EnumInterface>, string> Paths indexed by enum FQCN
     */
    private $enums;

    /** @var string|null */
    private $baseDir;

    /** @var string|null */
    private $libPath;

    public function __construct(array $enums = [], string $baseDir = null, string $libPath = null)
    {
        $this->enums = $enums;
        $this->baseDir = $baseDir;
        $this->libPath = $libPath;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate javascript enums')
            ->addArgument('enums', InputArgument::IS_ARRAY, 'The enums & paths of the files where to generate the javascript enums. Format: "enum FQCN:path"')
            ->addOption('base-dir', null, InputOption::VALUE_REQUIRED, 'A prefixed dir used for relative paths supplied for each of the generated enums and library path', $this->baseDir)
            ->addOption('lib-path', null, InputOption::VALUE_REQUIRED, 'The path of the file were to place the javascript library sources used by the dumped enums.', $this->libPath)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Elao Enums Javascript generator');

        $io->note(<<<TXT
This command is not meant to be used as part of an automatic process updating your code.
There is no BC promise on the generated code. Once generated, the code belongs to you.
TXT
        );

        $libPath = $input->getOption('lib-path');

        if (!$libPath) {
            throw new \InvalidArgumentException('Please provide the "--lib-path" option');
        }

        $enums = $this->enums;
        /** @var string[] $enumArgs */
        if ($enumArgs = $input->getArgument('enums')) {
            $enums = [];
            foreach ($enumArgs as $arg) {
                [$fqcn, $path] = explode(':', $arg, 2);
                $enums[$fqcn] = $path;
            }
        }

        $dumper = new JsDumper($libPath, $input->getOption('base-dir'));

        $io->comment("Generating library sources at path <info>{$dumper->normalizePath($libPath)}</info>");

        $dumper->dumpLibrarySources();

        foreach ($enums as $fqcn => $path) {
            $shortName = (new \ReflectionClass($fqcn))->getShortName();
            $io->comment("Generating <info>$shortName</info> enum at path <info>{$dumper->normalizePath($path)}</info>");
            $dumper->dumpEnumToFile($fqcn, $path);
        }

        return 0;
    }
}
