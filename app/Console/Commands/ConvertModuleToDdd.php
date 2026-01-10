<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class ConvertModuleToDdd extends Command
{
    protected $signature = 'module:convert-ddd {name : The name of the module (e.g. Blog)}';

    protected $description = 'Convert a basic module to DDD structure';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $kebabName = Str::kebab($name);
        $modulePath = base_path('app-modules/'.$kebabName);

        if (! File::exists($modulePath)) {
            $this->error("Module {$name} does not exist at {$modulePath}!");

            return;
        }

        $this->info("Converting module {$name} to DDD structure...");

        $this->createDirectories($modulePath);
        $this->moveAndRefactorServiceProvider($modulePath, $name);
        $this->moveRoutes($modulePath, $name);
        $this->createConfig($modulePath, $name);
        $this->updateComposerJson($modulePath, $name);
        $this->cleanup($modulePath);

        $this->info("Updating composer for modules/{$kebabName}...");
        Process::forever()->run("composer update modules/{$kebabName}", function (string $type, string $output) {
            $this->output->write($output);
        });

        $this->info("Module {$name} converted successfully!");
    }

    protected function createDirectories($path)
    {
        $dirs = [
            'config',
            'src/Application/CommandHandlers',
            'src/Application/Commands',
            'src/Application/DTOs',
            'src/Application/Queries',
            'src/Application/QueryContracts',
            'src/Application/QueryHandlers',
            'src/Domain/Entities',
            'src/Domain/Events',
            'src/Domain/Exceptions',
            'src/Domain/Repositories',
            'src/Domain/Services',
            'src/Domain/ValueObjects',
            'src/Infrastructure/Listeners',
            'src/Infrastructure/Persistence/Eloquent/Mappers',
            'src/Infrastructure/Persistence/Eloquent/Models',
            'src/Infrastructure/Persistence/Eloquent/ReadModels',
            'src/Infrastructure/Persistence/Eloquent/Repositories',
            'src/Infrastructure/Persistence/Eloquent/Rules',
            'src/Infrastructure/Providers',
            'src/Presentation/Controllers/Admin',
            'src/Presentation/Mappers',
            'src/Presentation/Policies',
            'src/Presentation/Requests',
            'src/Presentation/Routes',
            'tests/Feature',
            'tests/Unit/Domain',
        ];

        foreach ($dirs as $dir) {
            $fullPath = "{$path}/{$dir}";
            if (! File::exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
                // File::put("{$fullPath}/.gitkeep", '');
            }
        }
    }

    protected function moveAndRefactorServiceProvider($path, $name)
    {
        $kebabName = Str::kebab($name);
        $oldProviderPath = "{$path}/src/Providers/{$name}ServiceProvider.php";
        $newProviderPath = "{$path}/src/Infrastructure/Providers/{$name}ServiceProvider.php";

        if (File::exists($oldProviderPath)) {
            $content = File::get($oldProviderPath);

            // Change Namespace
            $content = str_replace(
                "namespace Modules\\{$name}\\Providers;",
                "namespace Modules\\{$name}\\Infrastructure\\Providers;",
                $content
            );

            // Add boilerplate if missing (simple check)
            if (! str_contains($content, 'loadRoutesFrom')) {
                $content = <<<PHP
<?php

namespace Modules\\{$name}\\Infrastructure\\Providers;

use Illuminate\Support\ServiceProvider;

class {$name}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        \$this->mergeConfigFrom(__DIR__.'/../../../config/{$kebabName}.php', '{$kebabName}');
    }

    public function boot(): void
    {
        if (\$this->app->runningInConsole()) {
            \$this->publishes([
                __DIR__.'/../../../config/{$kebabName}.php' => config_path('{$kebabName}.php'),
            ], '{$kebabName}-config');
        }

        \$this->loadRoutesFrom(__DIR__.'/../../Presentation/Routes/web.php');
    }
}
PHP;
            }

            File::put($newProviderPath, $content);
            File::delete($oldProviderPath);
        }
    }

    protected function moveRoutes($path, $name)
    {
        $kebabName = Str::kebab($name);
        $oldRoutePath = "{$path}/routes/{$kebabName}-routes.php";
        if (! File::exists($oldRoutePath)) {
            $oldRoutePath = "{$path}/routes/web.php";
        }

        $newRoutePath = "{$path}/src/Presentation/Routes/web.php";

        if (File::exists($oldRoutePath)) {
            File::move($oldRoutePath, $newRoutePath);
        } else {
            // Create if not exists
            File::put($newRoutePath, "<?php\n\nuse Illuminate\Support\Facades\Route;\n\nRoute::middleware(['web', 'auth'])->group(function () {
    // Routes
});");
        }
    }

    protected function createConfig($path, $name)
    {
        $kebabName = Str::kebab($name);
        $upperSnakeName = Str::upper(Str::snake($name));
        $configPath = "{$path}/config/{$kebabName}.php";

        if (! File::exists($configPath)) {
            $content = <<<PHP
<?php

return [
    'cache' => [
        'use_tags' => env('{$upperSnakeName}_MODULE_CACHE_TAGS', null),
        'ttl' => env('{$upperSnakeName}_MODULE_CACHE_TTL', 3600),
        'prefix' => '{$kebabName}:',
    ],

    'pagination' => [
        'default_per_page' => env('{$upperSnakeName}_MODULE_PER_PAGE', 15),
        'max_per_page' => env('{$upperSnakeName}_MODULE_MAX_PER_PAGE', 100),
    ],
];
PHP;
            File::put($configPath, $content);
        }
    }

    protected function updateComposerJson($path, $name)
    {
        $composerPath = "{$path}/composer.json";
        if (File::exists($composerPath)) {
            $content = File::get($composerPath);
            $json = json_decode($content, true);

            // Update ServiceProvider path
            $providerClass = "Modules\\{$name}\\Infrastructure\\Providers\\{$name}ServiceProvider";

            // Check if it was under "extra.laravel.providers"
            if (isset($json['extra']['laravel']['providers'])) {
                $json['extra']['laravel']['providers'] = [$providerClass];
            }

            File::put($composerPath, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }

    protected function cleanup($path)
    {
        // Remove directories
        $dirsToRemove = [
            'src/Providers',
            'routes',
            'resources',
        ];

        foreach ($dirsToRemove as $dir) {
            $fullPath = "{$path}/{$dir}";
            if (File::exists($fullPath)) {
                File::deleteDirectory($fullPath);
            }
        }
    }
}
