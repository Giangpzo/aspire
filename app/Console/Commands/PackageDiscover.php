<?php

namespace App\Console\Commands;

use App\Console\Commands\Extended\ProjectPackageManifest;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class PackageDiscover extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:discover-modules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extended package:discover of Laravel to discover Provider in Modules';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $manifest = new ProjectPackageManifest(new Filesystem(), base_path(), app()->getCachedPackagesPath());

        # Manifest Build
        $manifest->build();

        # Reload Manifest
        $manifest->aliases();

        foreach (array_keys($manifest->manifest) as $package) {
            $this->line("Discovered Package: <info>{$package}</info>");
        }

        $this->info('Package manifest generated successfully.');
    }
}