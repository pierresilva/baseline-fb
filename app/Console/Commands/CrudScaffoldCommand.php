<?php

namespace App\Console\Commands;

use App\Console\Commands\CrudScaffold\CrudScaffold;
use Illuminate\Support\Composer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CrudScaffoldCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'crud-scaffold:setup
                          {filePath=crud-scaffold.json : file path of setting json file. Default: crud-scaffold.json }
                          {--f|force : Allow overwrite files}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Setup crud-scaffold';

  /**
   * Crud-D-Scaffold Core
   *
   * @var obj
   */
  protected $crudScaffold;

  /**
   * @var Composer
   */
  private $composer;


  /**
   * Create a new command instance.
   *
   * @param CrudScaffold $crudScaffold
   * @param Composer $composer
   */
  public function __construct(CrudScaffold $crudScaffold, Composer $composer)
  {
    parent::__construct();
    $this->composer = $composer;
    $this->crudScaffold = $crudScaffold;
    $this->crudScaffold->setCommand($this);
  }

  /**
   * Execute the console command.
   *
   * @throws \Exception
   */
  public function handle()
  {

    $this->crudScaffold->generate();

    //Dump autoload
    $this->info('Dump-autoload...');
    $this->composer->dumpAutoloads();

    // End Message
    $this->info('Configuring is done');
  }
}
