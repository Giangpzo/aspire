<?php

namespace App\Console\Commands\Extended;

use Illuminate\Foundation\PackageManifest;
use function collect;

class ProjectPackageManifest extends PackageManifest
{
    public function build()
    {
        $packages = [];

        if ($this->files->exists($path = $this->vendorPath . '/composer/installed.json')) {
            $installed = json_decode($this->files->get($path), true);

            $packages = $installed['packages'] ?? $installed;
        }

        # Find Extra Package Discover in Modules Folder
        $moduleComposers = glob(base_path('app/Modules/*/composer.json'));
        foreach ($moduleComposers as $moduleComposer) {
            $packages[] = json_decode(file_get_contents($moduleComposer), true);
        }

        $ignoreAll = in_array('*', $ignore = $this->packagesToIgnore());

        $this->write(collect($packages)->mapWithKeys(function ($package) {
            return [$this->format($package['name']) => $package['extra']['laravel'] ?? []];
        })->each(function ($configuration) use (&$ignore) {
            $ignore = array_merge($ignore, $configuration['dont-discover'] ?? []);
        })->reject(function ($configuration, $package) use ($ignore, $ignoreAll) {
            return $ignoreAll || in_array($package, $ignore);
        })->filter()->all());
    }
}