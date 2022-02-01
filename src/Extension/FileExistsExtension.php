<?php

namespace App\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\Filesystem\Filesystem;

class FileExistsExtension extends AbstractExtension
{
private $fileSystem;
private $projectDir;

public function __construct(Filesystem $fileSystem, string $projectDir = "")
{
$this->fileSystem = $fileSystem;
$this->projectDir = $projectDir;
}

public function getFunctions(): array
{
return [
new TwigFunction('file_exists', [$this, 'fileExists']),
];
}

/**
    * @param string An absolute or relative to public folder path
    *
    * @return bool True if file exists, false otherwise
    */
    public function fileExists(string $path): bool
    {
    if (!$this->fileSystem->isAbsolutePath($path)) {
    $path = "{$this->projectDir}/public/{$path}";
    }

    return $this->fileSystem->exists($path);
    }
}