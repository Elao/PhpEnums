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
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\Extractor\ExtractorInterface;
use Symfony\Component\Translation\MessageCatalogue;

class EnumExtractor implements ExtractorInterface
{
    /**
     * @var array<string, string>
     */
    private $paths;

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
     * @param array<string, string> $paths
     * @param array<string>         $ignore
     */
    public function __construct(array $paths, string $domain, string $fileNamePattern, array $ignore)
    {
        $this->paths = $paths;
        $this->domain = $domain;
        $this->fileNamePattern = $fileNamePattern;
        $this->ignore = $ignore;
    }

    public function setPrefix($prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * @param array|string $resource
     */
    public function extract($resource, MessageCatalogue $catalog): void
    {
        // Ensure it runs only once.
        if ($this->hasRun) {
            return;
        }
        $this->hasRun = true;

        $finder = new Finder();

        foreach ($this->paths as $dir => $settings) {
            // Normalize namespace.
            $namespace = rtrim($settings['namespace'], '\\') . '\\';

            /** @var SplFileInfo $file */
            foreach ($finder->files()->name($this->fileNamePattern)->in($dir) as $file) {
                foreach ($this->ignore as $ignore) {
                    if (fnmatch($ignore, $file->getPathname())) {
                        continue 2;
                    }
                }

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
                foreach ($readables as $k => $enum) {
                    $enum = (string) $enum;
                    if ('' === $enum) {
                        continue;
                    }

                    $catalog->set($enum, $this->prefix . $enum, $this->domain);
                    $metadata = $catalog->getMetadata($enum, $this->domain) ?? [];
                    $normalizedFilename = preg_replace('{[\\\\/]+}', '/', $file->getPathName());
                    $metadata['sources'][] = $normalizedFilename . ':' . $k;
                    $catalog->setMetadata($enum, $metadata, $this->domain);
                }
            }
        }
    }
}
