<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeModuleDdd extends Command
{
    protected $signature = 'make:module-ddd {name : The name of the module (e.g. Blog)}';

    protected $description = 'Create a new DDD module by combining basic generation and DDD conversion';

    public function handle()
    {
        $name = $this->argument('name');

        $this->info("Step 1: Creating basic module '{$name}'...");
        // This command is provided by internachi/modular
        $this->call('make:module', [
            'name' => $name,
        ]);

        $this->info("Step 2: Converting '{$name}' to DDD structure...");
        // This command we created in ConvertModuleToDdd.php
        $this->call('module:convert-ddd', [
            'name' => $name,
        ]);

        $this->info("DDD Module '{$name}' created and registered successfully!");
    }
}
