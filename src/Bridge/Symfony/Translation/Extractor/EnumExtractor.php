<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Translation\Extractor;

use Elao\Enum\ReadableEnum;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\Glob;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\Extractor\ExtractorInterface;
use Symfony\Component\Translation\MessageCatalogue;

class EnumExtractor implements ExtractorInterface
{
    /**
     * @var array<string, string>
     */
    private $namespacesToDirs;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $fileNamePattern;

    /**
     * @var array<string>
     */
    private $ignore;

    /**
     * @var string
     */
    private $prefix = '';

    /**
     * @var bool
     */
    private $hasRun = false;

    /**
     * @param array<string, string> $namespacesToDirs
     * @param array<string>         $ignore
     */
    public function __construct(array $namespacesToDirs, string $domain, string $fileNamePattern, array $ignore)
    {
        $this->namespacesToDirs = $namespacesToDirs;
        $this->domain = $domain;
        $this->fileNamePattern = $fileNamePattern;
        $this->ignore = $ignore;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param array|string $resource
     */
    public function extract($resource, MessageCatalogue $catalog)
    {
        // Ensure it runs only once.
        if ($this->hasRun) {
            return;
        }
        $this->hasRun = true;

        $finder = new Finder();

        foreach ($this->namespacesToDirs as $namespace => $dir) {
            // Normalize namespace.
            $namespace = rtrim($namespace, '\\') . '\\';

            foreach ($this->ignore as $ignore) {
                $finder->notPath(Glob::toRegex(str_replace(rtrim($dir, '\\/') . '/', '', $ignore)));
            }

            /** @var SplFileInfo $file */
            foreach ($finder->in($dir)->files()->name($this->fileNamePattern) as $file) {
                // Get file pathinfo and clear dirname.
                $path = pathinfo($file->getRelativePathname());
                if ($path['dirname'] === '.') {
                    $path['dirname'] = '';
                } else {
                    $path['dirname'] .= '/';
                }

                // Build class name and check if it's a ReadableEnum instance.
                /** @var ReadableEnum $class */
                $class = $namespace . strtr($path['dirname'] . $path['filename'], ['/' => '\\']);
                if (!is_a($class, ReadableEnum::class, true)) {
                    continue;
                }

                $readables = $class::readables();
                foreach ($readables as $enumValue => $translationKey) {
                    if ('' === $translationKey) {
                        continue;
                    }

                    $catalog->set($translationKey, $this->prefix . $translationKey, $this->domain);
                    $metadata = $catalog->getMetadata($translationKey, $this->domain) ?? [];
                    $normalizedFilename = preg_replace('{[\\\\/]+}', '/', $file->getPathName());
                    $metadata['sources'][] = $normalizedFilename . ':' . $enumValue;
                    $catalog->setMetadata($translationKey, $metadata, $this->domain);
                }
            }
        }
    }
}
