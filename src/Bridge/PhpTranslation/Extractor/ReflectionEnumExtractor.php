<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\PhpTranslation\Extractor;

use Elao\Enum\ReadableEnumInterface;
use Translation\Extractor\Model\SourceCollection;
use Translation\Extractor\Model\SourceLocation;

final class ReflectionEnumExtractor
{
    /**
     * Callable returning list of FCQN candidates for Enums.
     * It's necessary for files containing these classes to be already loaded.
     *
     * @var callable
     */
    private $classLoader;

    public function __construct(callable $classLoader = null)
    {
        $this->classLoader = $classLoader ?: 'get_declared_classes';
    }

    public function getSourceLocations(SourceCollection $sourceCollection)
    {
        foreach (call_user_func($this->classLoader) as $class) {
            if (!is_subclass_of($class, ReadableEnumInterface::class)) {
                continue;
            }

            $reflectionClass = new \ReflectionClass($class);
            if ($reflectionClass->isAbstract()) {
                continue;
            }

            $line = $reflectionClass->getMethod('readables')->getStartLine();

            /** @var ReadableEnumInterface $class */
            foreach ($class::readables() as $value => $label) {
                $sourceCollection->addLocation(new SourceLocation($label, $reflectionClass->getFileName(), $line));
            }
        }
    }
}
