<?php

namespace SteelAnts\LaravelBoilerplate\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'auth:install
                            {--force : Overwrite existing}';

    protected $description = 'Install Authentication';

    public function handle(): void
    {
        $this->components->info('Installing Laravel-Auth Scaffolding');
        self::exportStubs('app');
    }

    protected function exportStubs($type = "app")
    {
        $baseDir = __DIR__ . '/../../..';
        $moduleSubPath = ('/stubs/' . $type);
        $laravelSubPath = ('/' . $type);
        $moduleRootPath = realpath($baseDir . $moduleSubPath);

        foreach (File::allFiles($moduleRootPath) as $file) {
            $laravelViewRoot = str_replace($moduleRootPath, $laravelSubPath, $file->getPath());
            $stubFullPath = ($file->getPath() . "/" . $file->getFilename());
            $viewFullPath = (base_path($laravelViewRoot) . "/" . str_replace('.stub', '.php', $file->getFilename()));

            $this->checkDirectory(dirname($viewFullPath));

            if (file_exists($viewFullPath) && !$this->option('force')) {
                if (!$this->components->confirm("The [" . $laravelViewRoot . '/' . $file->getFilename() . "] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy($stubFullPath, $viewFullPath);
        }
    }

    protected function checkDirectory($directory)
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }
}
