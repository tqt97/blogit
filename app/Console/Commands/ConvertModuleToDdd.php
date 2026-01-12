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
        $this->createBoilerplateFiles($modulePath, $name);
        $this->updateComposerJson($modulePath, $name);
        $this->cleanup($modulePath);

        $this->info("Updating composer for modules/{$kebabName}...");
        Process::forever()->run("composer update modules/{$kebabName}", function (string $type, string $output) {
            $this->output->write($output);
        });

        $this->info("Module {$name} converted successfully!");
    }

    protected function createBoilerplateFiles($path, $name)
    {
        $this->createStandardPorts($path, $name);
        $this->createDomainFiles($path, $name);
        $this->createApplicationFiles($path, $name);
        $this->createInfrastructureFiles($path, $name);
        $this->createPresentationFiles($path, $name);
    }

    protected function createApplicationFiles($path, $name)
    {
        // DTO
        $this->createFileIfNotExists(
            "{$path}/src/Application/DTOs/{$name}DTO.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Application\\DTOs;

final readonly class {$name}DTO
{
    public function __construct(
        public int \$id,
    ) {}
}
PHP
        );
    }

    protected function createPresentationFiles($path, $name)
    {
        $kebabName = Str::kebab($name);

        // Store Request
        $this->createFileIfNotExists(
            "{$path}/src/Presentation/Requests/Store{$name}Request.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Presentation\\Requests;

use Illuminate\\Foundation\\Http\\FormRequest;

class Store{$name}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Define rules
        ];
    }
}
PHP
        );

        // Update Request
        $this->createFileIfNotExists(
            "{$path}/src/Presentation/Requests/Update{$name}Request.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Presentation\\Requests;

use Illuminate\\Foundation\\Http\\FormRequest;

class Update{$name}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Define rules
        ];
    }
}
PHP
        );

        // Command Mapper
        $this->createFileIfNotExists(
            "{$path}/src/Presentation/Mappers/Create{$name}CommandMapper.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Presentation\\Mappers;

// use Modules\\{$name}\\Application\\Commands\\Create{$name}Command;

final class Create{$name}CommandMapper
{
    public function __invoke(array \$data) // : Create{$name}Command
    {
        // return new Create{$name}Command(...);
    }
}
PHP
        );

        // Policy
        $this->createFileIfNotExists(
            "{$path}/src/Presentation/Policies/{$name}Policy.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Presentation\\Policies;

use App\\Models\\User;
use Illuminate\\Auth\\Access\\HandlesAuthorization;
use Modules\\{$name}\\Domain\\Entities\\{$name};

class {$name}Policy
{
    use HandlesAuthorization;

    public function viewAny(User \$user): bool
    {
        return true;
    }

    public function create(User \$user): bool
    {
        return true;
    }

    public function update(User \$user, ?{$name} \$model = null): bool
    {
        return true;
    }

    public function delete(User \$user, ?{$name} \$model = null): bool
    {
        return true;
    }
}
PHP
        );

        // Controller (Updated)
        $this->createFileIfNotExists(
            "{$path}/src/Presentation/Controllers/Admin/{$name}Controller.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Presentation\\Controllers\\Admin;

use Illuminate\\Http\\RedirectResponse;
use Illuminate\\Support\\Facades\\Gate;
use Inertia\\Inertia;
use Inertia\\Response;
use Modules\\{$name}\\Domain\\Entities\\{$name};
use Modules\\{$name}\\Presentation\\Requests\\Store{$name}Request;
use Modules\\{$name}\\Presentation\\Requests\\Update{$name}Request;

final class {$name}Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', {$name}::class);

        return Inertia::render('admin/{$kebabName}/index');
    }

    public function create(): Response
    {
        Gate::authorize('create', {$name}::class);

        return Inertia::render('admin/{$kebabName}/create');
    }

    public function store(Store{$name}Request \$request): RedirectResponse
    {
        Gate::authorize('create', {$name}::class);

        return redirect()->route('{$kebabName}.index');
    }

    public function edit(int \$id): Response
    {
        Gate::authorize('update', {$name}::class);

        return Inertia::render('admin/{$kebabName}/edit');
    }

    public function update(int \$id, Update{$name}Request \$request): RedirectResponse
    {
        Gate::authorize('update', {$name}::class);

        return back();
    }

    public function destroy(int \$id): RedirectResponse
    {
        Gate::authorize('delete', {$name}::class);

        return back();
    }
}
PHP
        );
    }

    protected function createStandardPorts($path, $name)
    {
        // EventBus Port
        $this->createFileIfNotExists(
            "{$path}/src/Application/Ports/EventBus/EventBus.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Application\\Ports\\EventBus;

interface EventBus
{
    /** @param list<object> \$events */
    public function publish(array \$events): void;
}
PHP
        );

        // TransactionManager Port
        $this->createFileIfNotExists(
            "{$path}/src/Application/Ports/Transaction/TransactionManager.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Application\\Ports\\Transaction;

interface TransactionManager
{
    /** @template T */
    public function withinTransaction(callable \$fn): mixed;
}
PHP
        );
    }

    protected function createDomainFiles($path, $name)
    {
        // Entity
        $this->createFileIfNotExists(
            "{$path}/src/Domain/Entities/{$name}.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Domain\\Entities;

use Modules\\{$name}\\Domain\\ValueObjects\\{$name}Id;

final class {$name}
{
    /** @var list<object> */
    private array \$events = [];

    private function __construct(
        private readonly ?{$name}Id \$id,
    ) {}

    public static function create(): self
    {
        return new self(null);
    }

    public static function reconstitute({$name}Id \$id): self
    {
        return new self(\$id);
    }

    public function id(): ?{$name}Id
    {
        return \$this->id;
    }

    public function pullEvents(): array
    {
        \$events = \$this->events;
        \$this->events = [];

        return \$events;
    }

    private function record(object \$event): void
    {
        \$this->events[] = \$event;
    }
}
PHP
        );

        // ID Value Object
        $this->createFileIfNotExists(
            "{$path}/src/Domain/ValueObjects/{$name}Id.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Domain\\ValueObjects;

use InvalidArgumentException;

final class {$name}Id
{
    public function __construct(private readonly int \$value)
    {
        if (\$value <= 0) {
            throw new InvalidArgumentException('Id must be positive.');
        }
    }

    public function value(): int
    {
        return \$this->value;
    }
}
PHP
        );

        // Repository Interface
        $this->createFileIfNotExists(
            "{$path}/src/Domain/Repositories/{$name}Repository.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Domain\\Repositories;

use Modules\\{$name}\\Domain\\Entities\\{$name};
use Modules\\{$name}\\Domain\\ValueObjects\\{$name}Id;

interface {$name}Repository
{
    public function save({$name} \$entity): {$name};

    public function find({$name}Id \$id): ?{$name};

    public function delete({$name}Id \$id): void;
}
PHP
        );

        $this->createCommonValueObjects($path, $name);
        $this->createDomainEvents($path, $name);
    }

    protected function createCommonValueObjects($path, $name)
    {
        // Intent Enum
        $this->createFileIfNotExists(
            "{$path}/src/Domain/ValueObjects/Intent.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Domain\\ValueObjects;

enum Intent: string
{
    case Default = 'default';
    case CreateAndContinue = 'create_and_continue';

    public static function fromString(?string \$value, self \$default = self::Default): self
    {
        if (! is_string(\$value)) {
            return \$default;
        }

        return self::tryFrom(trim(\$value)) ?? \$default;
    }
}
PHP
        );

        // Pagination
        $this->createFileIfNotExists(
            "{$path}/src/Domain/ValueObjects/Pagination.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Domain\\ValueObjects;

use InvalidArgumentException;

final readonly class Pagination
{
    public function __construct(
        public int \$page,
        public int \$perPage,
        public int \$maxPerPage = 100,
    ) {
        if (\$this->page < 1) {
            throw new InvalidArgumentException('Page must be >= 1.');
        }
    }

    public static function fromInts(?int \$page, ?int \$perPage, int \$default = 15): self
    {
        return new self(\$page ?? 1, \$perPage ?? \$default);
    }
}
PHP
        );

        // Sorting
        $this->createFileIfNotExists(
            "{$path}/src/Domain/ValueObjects/Sorting.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Domain\\ValueObjects;

final readonly class Sorting
{
    public function __construct(
        public string \$field = 'id',
        public string \$direction = 'desc',
    ) {}
}
PHP
        );
    }

    protected function createDomainEvents($path, $name)
    {
        // Created
        $this->createFileIfNotExists(
            "{$path}/src/Domain/Events/{$name}Created.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Domain\\Events;

use Modules\\{$name}\\Domain\\Entities\\{$name};

final readonly class {$name}Created
{
    public function __construct(public {$name} \$entity) {}
}
PHP
        );

        // Updated
        $this->createFileIfNotExists(
            "{$path}/src/Domain/Events/{$name}Updated.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Domain\\Events;

use Modules\\{$name}\\Domain\\Entities\\{$name};

final readonly class {$name}Updated
{
    public function __construct(public {$name} \$entity) {}
}
PHP
        );

        // Deleted
        $this->createFileIfNotExists(
            "{$path}/src/Domain/Events/{$name}Deleted.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Domain\\Events;

use Modules\\{$name}\\Domain\\ValueObjects\\{$name}Id;

final readonly class {$name}Deleted
{
    public function __construct(public {$name}Id \$id) {}
}
PHP
        );
    }

    protected function createInfrastructureFiles($path, $name)
    {
        $kebabName = Str::kebab($name);
        $pluralKebab = Str::plural($kebabName);

        // Laravel EventBus Adapter
        $this->createFileIfNotExists(
            "{$path}/src/Infrastructure/Bus/Events/LaravelEventBus.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Infrastructure\\Bus\\Events;

use Illuminate\\Support\\Facades\\Event;
use Modules\\{$name}\\Application\\Ports\\EventBus\\EventBus;

final class LaravelEventBus implements EventBus
{
    public function publish(array \$events): void
    {
        foreach (\$events as \$event) {
            Event::dispatch(\$event);
        }
    }
}
PHP
        );

        // DB Transaction Adapter
        $this->createFileIfNotExists(
            "{$path}/src/Infrastructure/Transaction/DbTransactionManager.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Infrastructure\\Transaction;

use Illuminate\\Support\\Facades\\DB;
use Modules\\{$name}\\Application\\Ports\\Transaction\\TransactionManager;

final class DbTransactionManager implements TransactionManager
{
    public function withinTransaction(callable \$fn): mixed
    {
        return DB::transaction(\$fn);
    }
}
PHP
        );

        // Eloquent Model
        $this->createFileIfNotExists(
            "{$path}/src/Infrastructure/Persistence/Eloquent/Models/{$name}Model.php",
            <<<PHP
<?php

namespace Modules\\{$name}\\Infrastructure\\Persistence\\Eloquent\\Models;

use Illuminate\\Database\\Eloquent\\Model;

class {$name}Model extends Model
{
    protected \$table = '{$pluralKebab}';

    protected \$guarded = [];
}
PHP
        );

        // Entity Mapper
        $this->createFileIfNotExists(
            "{$path}/src/Infrastructure/Persistence/Eloquent/Mappers/{$name}Mapper.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Infrastructure\\Persistence\\Eloquent\\Mappers;

use Modules\\{$name}\\Domain\\Entities\\{$name};
use Modules\\{$name}\\Domain\\ValueObjects\\{$name}Id;
use Modules\\{$name}\\Infrastructure\\Persistence\\Eloquent\\Models\\{$name}Model;

final class {$name}Mapper
{
    public function toEntity({$name}Model \$model): {$name}
    {
        return {$name}::reconstitute(
            id: new {$name}Id((int) \$model->id),
        );
    }

    public function toPersistence({$name} \$entity, ?{$name}Model \$model = null): {$name}Model
    {
        \$model ??= new {$name}Model();
        // \$model->name = \$entity->name()->value();
        return \$model;
    }
}
PHP
        );

        // Cache Invalidator Listener
        $this->createFileIfNotExists(
            "{$path}/src/Infrastructure/Listeners/{$name}CacheInvalidator.php",
            <<<PHP
<?php

declare(strict_types=1);

namespace Modules\\{$name}\\Infrastructure\\Listeners;

use Illuminate\\Contracts\\Cache\\Repository as CacheRepository;
use Illuminate\\Events\\Dispatcher;

final class {$name}CacheInvalidator
{
    public function __construct(private readonly CacheRepository \$cache)
    {
    }

    public function subscribe(Dispatcher \$events): void
    {
        // \$events->listen(SomeEvent::class, [\$this, 'handle']);
    }
}
PHP
        );
    }

    protected function createFileIfNotExists($path, $content)
    {
        $directory = dirname($path);
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (! File::exists($path)) {
            File::put($path, $content);
        }
    }

    protected function createDirectories($path)
    {
        $dirs = [
            'config',
            'src/Application/CommandHandlers',
            'src/Application/Commands',
            'src/Application/DTOs',
            'src/Application/Ports',
            'src/Application/Queries',
            'src/Application/QueryHandlers',
            'src/Domain/Entities',
            'src/Domain/Events',
            'src/Domain/Exceptions',
            'src/Domain/Repositories',
            'src/Domain/Services',
            'src/Domain/ValueObjects',
            'src/Infrastructure/Bus',
            'src/Infrastructure/Listeners',
            'src/Infrastructure/Persistence/Eloquent/Mappers',
            'src/Infrastructure/Persistence/Eloquent/Models',
            'src/Infrastructure/Persistence/Eloquent/ReadModels',
            'src/Infrastructure/Persistence/Eloquent/Repositories',
            'src/Infrastructure/Providers',
            'src/Infrastructure/Transaction',
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

            // Ensure extra.laravel.providers exists
            if (! isset($json['extra']['laravel']['providers'])) {
                $json['extra']['laravel']['providers'] = [];
            }

            // Append if not present
            if (! in_array($providerClass, $json['extra']['laravel']['providers'])) {
                $json['extra']['laravel']['providers'] = $providerClass;
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
