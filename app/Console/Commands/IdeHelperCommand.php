<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class IdeHelperCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ide-helper:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate all ide helpers.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            \Artisan::call('ide-helper:generate');
            $this->info('_ide_helper.php Generated');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }

        try {
            \Artisan::call('ide-helper:meta');
            $this->info('.phpstorm.meta.php Generated');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }

        try {
            \Artisan::call('ide-helper:models', ['--write' => true]);
            $this->info('phpDocBlock to models Generated');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }

    }
}
