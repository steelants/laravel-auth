<?php

namespace SteelAnts\LaravelAuth\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'install:auth
                            {--force : Overwrite existing files by default}';

    protected $description = 'Install Authentication scaffolding';

    public function handle(): void
    {
        $this->components->info('Installing Laravel-Auth Scaffolding');
        self::exportStubs('app');
        self::exportStubs('resources');

        $this->components->info('Adding Routes');
        self::appendRoutes();
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

    protected function appendRoutes(string $RouteType = "web")
    {
        $RouteFilePath = base_path('routes/' . $RouteType . '.php');

        if (strpos(file_get_contents($RouteFilePath), 'Route::auth();') !== false) {
            return;
        }

        file_put_contents($RouteFilePath, "Route::auth();", FILE_APPEND);
    }

    protected function checkDirectory($directory)
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }
}
