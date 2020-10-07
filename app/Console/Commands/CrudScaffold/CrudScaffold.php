<?php


namespace App\Console\Commands\CrudScaffold;


use App\Console\Commands\CrudScaffoldCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CrudScaffold
{

  private $files;     /* Filesystem */
  private $command;   /* CrudDScaffoldSetupCommand */
  private $data;   /* Data */

  //private $app_type;  /* 'web' or 'api' */

  /**
   * Create a new command instance.
   *
   * @param Filesystem $files
   */
  public function __construct(Filesystem $files)
  {
    $this->files = $files;
  }

  public function setCommand(CrudScaffoldCommand $command)
  {
    $this->command = $command;
  }

  public function generate()
  {

    $this->command->info('Load Data...');
    $this->data = new Data($this->command, $this->files);
    $this->data->loadData();
    $this->command->info('Now Generating...');
    // $this->stubTest();
    // $this->setupProviders();
    $this->setupMigration();
    $this->setupSeeding();
    $this->setupModel();
    $this->setupController();
    // $this->setupViewLayout();
    // $this->setupView();
    $this->setupRoute();
    // $this->setupJsScss();
  }

  private function stubTest()
  {
    $this->command->info("\n" . 'Testing...');
    $stubTxt = $this->files->get(__DIR__ . '/Stubs/test3.stub');
    $outputPath = base_path() . '/app/test.php';
    $stub_obj = new StubCompiler($stubTxt, $this->data->models[1]->relations[0]);
    $output = $stub_obj->compile();
    $this->files->put($outputPath, $output);
    dd('test is end');
  }

  private function setupProviders()
  {
    // app/Providers/AppServiceProvider.php
    $outputPath = base_path() . '/app/Providers/AppServiceProvider.php';
    $original_src = $this->files->get($outputPath);
    $output = $original_src;

    $add_src = $this->files->get(__DIR__ . '/Stubs/app/Providers/AppServiceProvider_header.stub');
    $replace_pattern = '#(use Illuminate\\\Support\\\ServiceProvider;)#';
    $output = preg_replace($replace_pattern, '$1' . $add_src, $output);

    $add_src = $this->files->get(__DIR__ . '/Stubs/app/Providers/AppServiceProvider_function_boot.stub');
    $stub_obj = new StubCompiler($add_src, $this->data);
    $add_src = $stub_obj->compile();
    $replace_pattern = '#(public function boot\(\))(\n|\r|\r\n)(\s*\{)(\n|\r|\r\n)([^\}]*\})#';
    $output = preg_replace($replace_pattern, '$1$2$3' . $add_src . '$5', $output);

    if (!strpos($original_src, $add_src)) {
      $this->files->put($outputPath, $output);
    }
  }

  private function setupMigration()
  {

    foreach ($this->data->models as $model) {

      //table exist check
      if (Schema::hasTable(NameResolver::solveName($model->name, 'name_names'))) {    //table exists

        throw new \Exception('[' . NameResolver::solveName($model->name, 'name_names') . '] table is already exists. migrate:rollback and delete migration files');
      }

      // this case means two state
      // first state is created migration file and not migrate.
      // second state is not-created migration.
      // this program ignore first state.

      // case using laravel auth
      if ($this->data->use_laravel_auth === true && $model->name === "user") {
        $this->deleteMigration('add_columns_to_users_table.php');
        $stubTxt = $this->files->get(__DIR__ . '/Stubs/database/migrations/Auth/yyyy_mm_dd_hhmmss_add_columns_to_users_table.stub');
        $outputPath = base_path() . '/database/migrations/' . date('Y_m_d_His') . '_add_columns_to_users_table.php';
        $stub_obj = new StubCompiler($stubTxt, $model);
        $output = $stub_obj->compile();
        $this->files->put($outputPath, $output);

      } else {

        //create migration file
        $stubTxt = $this->files->get(__DIR__ . '/Stubs/database/migrations/yyyy_mm_dd_hhmmss_create_[model]_table.stub');

        if ($model->is_pivot) {
          $this->deleteMigration(NameResolver::solveName($model->name, 'name_name') . '_table.php');
          $outputPath = base_path() . '/database/migrations/' . date('Y_m_d_His') . '_create_' . NameResolver::solveName($model->name, 'name_name') . '_table.php';
        } else {
          $this->deleteMigration(NameResolver::solveName($model->name, 'name_names') . '_table.php');
          $outputPath = base_path() . '/database/migrations/' . date('Y_m_d_His') . '_create_' . NameResolver::solveName($model->name, 'name_names') . '_table.php';
        }
        $stub_obj = new StubCompiler($stubTxt, $model);
        $output = $stub_obj->compile();
        $this->files->put($outputPath, $output);
      }
    }
  }

  public function deleteMigration($migrationName)
  {
    $migrationsPath = database_path('migrations/');

    $migrations = collect(File::allFiles($migrationsPath));

    foreach ($migrations as $migration) {
      if (Str::endsWith($migration->getRealPath(), $migrationName)) {
        File::delete($migration->getRealPath());
      }
    }

  }

  private function setupSeeding()
  {

    foreach ($this->data->models as $model) {

      // (i) /database/seeders/DatabaseSeeder.php
      $stubTxt = $this->files->get(__DIR__ . '/Stubs/database/seeders/DatabaseSeeder_add.stub');
      $outputPath = base_path() . '/database/seeders/DatabaseSeeder.php';
      $stub_obj = new StubCompiler($stubTxt, $model);
      $add_src = $stub_obj->compile();

      $original_src = $this->files->get(base_path() . '/database/seeders/DatabaseSeeder.php');
      $replace_pattern = '#(public function run\(\)\s*\{)([^\}]*)(    \})#';
      $output = preg_replace($replace_pattern, '$1$2' . $add_src . '$3', $original_src);

      if (!strpos($original_src, $add_src)) {
        $this->files->put($outputPath, $output);
      }

      // (ii) /database/seeders/[Models]TableSeeder.php
      $stubTxt = $this->files->get(__DIR__ . '/Stubs/database/seeders/[Models]TableSeeder.stub');
      if ($model->is_pivot) {
        $outputPath = base_path() . '/database/seeders/' . NameResolver::solveName($model->name, 'NameName') . 'TableSeeder.php';
      } else {
        $outputPath = base_path() . '/database/seeders/' . NameResolver::solveName($model->name, 'NameNames') . 'TableSeeder.php';
      }
      $stub_obj = new StubCompiler($stubTxt, $model);
      $output = $stub_obj->compile();

      //overwrite check
      if (!$this->command->option('force')) {   // no check if force option is selected
        if ($this->files->exists($outputPath)) {
          throw new \Exception("Seed File is already exists![" . $outputPath . "]");
        }
      }
      $this->files->put($outputPath, $output);
    }
  }


  private function setupModel()
  {

    foreach ($this->data->models as $model) {

      if ($model->is_pivot) {
        continue;
      }

      // case using laravel auth
      if ($this->data->use_laravel_auth === true && $model->name === "user") {

        $outputPath = base_path() . '/app/Models/User.php';
        $original_src = $this->files->get($outputPath);
        $output = $original_src;

        $stubTxt = $this->files->get(__DIR__ . '/Stubs/app/Auth/User01_use.stub');
        $replace_pattern = '#(class User)#';
        if (!strpos($original_src, $stubTxt)) {
          $output = preg_replace($replace_pattern, $stubTxt . '$1', $output);
        }

        $stubTxt = $this->files->get(__DIR__ . '/Stubs/app/Auth/User02_use.stub');
        $replace_pattern = '#(use Notifiable;)#';
        if (!strpos($original_src, $stubTxt)) {
          $output = preg_replace($replace_pattern, '$1' . $stubTxt, $output);
        }

        $stubTxt = $this->files->get(__DIR__ . '/Stubs/app/Auth/User03_mass_assignment.stub');
        $replace_pattern = '#(\'name\', \'email\', \'password\',)#';
        if (!strpos($original_src, $stubTxt)) {
          $output = preg_replace($replace_pattern, '$1' . $stubTxt, $output);
        }

        $stubTxt = $this->files->get(__DIR__ . '/Stubs/app/Auth/User04_others.stub');
        $replace_pattern = '#(\}\s*)$#';
        if (!strpos($original_src, $stubTxt)) {
          $output = preg_replace($replace_pattern, $stubTxt . '$1', $output);
        }

        $stub_obj = new StubCompiler($output, $model);
        $output = $stub_obj->compile();

        $this->files->put($outputPath, $output);

      } else {

        //create model file
        $stubTxt = $this->files->get(__DIR__ . '/Stubs/app/Models/[Model].stub');
        $outputPath = base_path() . '/app/Models/' . NameResolver::solveName($model->name, 'NameName') . '.php';
        $stub_obj = new StubCompiler($stubTxt, $model);
        $output = $stub_obj->compile();

        //overwrite check
        if (!$this->command->option('force')) {   // no check if force option is selected
          if ($this->files->exists($outputPath)) {
            throw new \Exception("Model File is already exists![" . $outputPath . "]");
          }
        }
        $this->files->put($outputPath, $output);
      }
    }
  }


  private function setupController()
  {

    foreach ($this->data->models as $model) {

      if ($model->is_pivot) {
        continue;
      }

      //create controller file
      $stubTxt = $this->files->get(__DIR__ . '/Stubs/app/Http/Controllers/[Model]Controller.stub');
      $outputPath = base_path() . '/app/Http/Controllers/' . NameResolver::solveName($model->name, 'NameName') . 'Controller.php';
      $stub_obj = new StubCompiler($stubTxt, $model);
      $output = $stub_obj->compile();

      //overwrite check
      if (!$this->command->option('force')) {
        if ($this->files->exists($outputPath)) {
          throw new \Exception("Controller File is already exists![" . $outputPath . "]");
        }
      }
      $this->files->put($outputPath, $output);
    }
  }


  private function setupViewLayout()
  {

    //(i)layout --------------------------------------------------

    $stubTxt = $this->files->get(__DIR__ . '/Stubs/resources/views/layouts/de_app.blade.stub');
    $output_folder = base_path() . '/resources/views/layouts';
    $outputPath = $output_folder . '/de_app.blade.php';

    if (!$this->files->exists($output_folder)) {
      $this->files->makeDirectory($output_folder);
    }
    $this->files->put($outputPath, $stubTxt);

    //(ii)alert --------------------------------------------------

    // $stubTxt = $this->files->get( __DIR__. '/Stubs/resources/views/_common/alert.blade.stub');
    // $output_dir = base_path().'/resources/views/_common/';
    // $outputPath = $output_dir.'alert.blade.php';

    // //overwrite check
    // if( !$this->command->option('force') ){
    //     if( $this->files->exists($outputPath) ){
    //         throw new \Exception("Controller File is already exists![".$outputPath."]");
    //     }
    // }

    // //create directory
    // if( !$this->files->exists($output_dir) ){
    //     $this->files->makeDirectory( $output_dir, $mode = 493, $recursive = false, $force = false);
    // }
    // $this->files->put($outputPath, $stubTxt );

    //(iii)navi --------------------------------------------------

    $setting_array = $this->data;

    // check auth scaffold is done
    /*
            if( $this->checkAuthScaffold() ){
                $setting_array['auth'] = "true";
            }
    */
    $stubTxt = $this->files->get(__DIR__ . '/Stubs/resources/views/layouts/de_navi.blade.stub');
    $outputPath = base_path() . '/resources/views/layouts/de_navi.blade.php';
    $stub_obj = new StubCompiler($stubTxt, $setting_array);
    $output = $stub_obj->compile();

    $this->files->put($outputPath, $output);

    //(iv)authview --------------------------------------------------

    // check auth scaffold is done
    if ($this->checkAuthScaffold()) {
      /* later
                  $original_path_array = [
                      base_path().'/resources/views/home.blade.php',
                      base_path().'/resources/views/auth/login.blade.php',
                      base_path().'/resources/views/auth/register.blade.php',
                      base_path().'/resources/views/auth/passwords/email.blade.php',
                      base_path().'/resources/views/auth/passwords/reset.blade.php'
                  ];

                  foreach( $original_path_array as $original_path ){
                      $original_src = $this->files->get( $original_path );
                      $replaced_src = str_replace( "@extends('layouts.app')", "@extends('layout')", $original_src );

                      //overwrite check
                      if( !$this->command->option('force') ){
                          if( $this->files->exists($original_path) ){
                              throw new \Exception("Controller File is already exists![".$original_path."]");
                          }
                      }

                      $this->files->put( $original_path, $replaced_src );
                  }
      */
    }
  }


  private function checkAuthScaffold()
  {
    if ($this->files->exists(base_path() . '/resources/views/auth/login.blade.php')) {
      return true;
    } else {
      return false;
    }
  }


  private function setupView()
  {

    $view_filename_array = ['_form.blade', '_table.blade', 'create.blade', 'duplicate.blade', 'edit.blade', 'index.blade', 'show.blade'];

    foreach ($this->data->models as $model) {

      if ($model->name === 'user' && $this->data->use_laravel_auth === true) {
        /* later
                        $outputPath = base_path().'/resources/views/auth/register.blade.php';
                        $original_src = $this->files->get( $outputPath );
                        $output = $original_src;

                        $stubTxt = $this->files->get( __DIR__. '/Stubs/resources/views/auth/register_add.stub');
                        $replace_pattern = '#(.*)(<div class="form-group">)(.*?)(Register)#s';
                        $output = preg_replace ( $replace_pattern, '$1'.$stubTxt.'$2$3$4', $output );

                        $stub_obj = new StubCompiler( $output, $model );
                        $output = $stub_obj->compile();

                        $this->files->put($outputPath, $output );
        */
      }

      foreach ($view_filename_array as $view_filename) {
        $stubTxt = $this->files->get(__DIR__ . '/Stubs/resources/views/[models]/' . $view_filename . '.stub');
        $output_dir = base_path() . '/resources/views/' . NameResolver::solveName($model->name, 'nameNames') . '/';
        $output_filename = $view_filename . '.php';
        $outputPath = $output_dir . $output_filename;
        $stub_obj = new StubCompiler($stubTxt, $model);
        $output = $stub_obj->compile();

        //overwrite check
        if (!$this->command->option('force')) {
          if ($this->files->exists($outputPath)) {
            throw new \Exception("View File is already exists![" . $outputPath . "]");
          }
        }

        //create directory
        if (!$this->files->exists($output_dir)) {
          $this->files->makeDirectory($output_dir, $mode = 493, $recursive = false, $force = false);
        }
        $this->files->put($outputPath, $output);
      }
    }
  }


  private function setupRoute()
  {

    $stubTxt = $this->files->get(__DIR__ . '/Stubs/routes/api_add.stub');
    $outputPath = base_path() . '/routes/api.php';
    $stub_obj = new StubCompiler($stubTxt, $this->data);
    $output = $stub_obj->compile();
    $target_src = $this->files->get(base_path() . '/routes/api.php');
    if (!strpos($target_src, $output)) {
      $this->files->append($outputPath, $output);
    }
  }


  private function setupJsScss()
  {

    // js - cleanQuery
    $this->files->copy(__DIR__ . '/Stubs/resources/js/jquery.cleanQuery.js', base_path() . '/resources/js/jquery.cleanQuery.js');
    // js - dy.js
    $this->files->copy(__DIR__ . '/Stubs/resources/js/dog-ears.js', base_path() . '/resources/js/dog-ears.js');
    // sass - dy.scss
    $this->files->copy(__DIR__ . '/Stubs/resources/sass/_dog-ears.scss', base_path() . '/resources/sass/_dog-ears.scss');

    // js - app.js
    $output = $this->files->get(__DIR__ . '/Stubs/resources/js/app_add.js.stub');
    $outputPath = base_path() . '/resources/js/app.js';
    $target_src = $this->files->get($outputPath);
    if (!strpos($target_src, $output)) {
      $this->files->append($outputPath, $output);
    }

    // sass - app.scss
    $output = $this->files->get(__DIR__ . '/Stubs/resources/sass/app_add.scss.stub');
    $outputPath = base_path() . '/resources/sass/app.scss';
    $target_src = $this->files->get($outputPath);
    if (!strpos($target_src, $output)) {
      $this->files->append($outputPath, $output);
    }
  }

}
