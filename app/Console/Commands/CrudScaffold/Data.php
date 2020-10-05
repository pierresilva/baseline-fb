<?php


namespace App\Console\Commands\CrudScaffold;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use App\Console\Commands\CrudScaffoldCommand;

class Data
{

  private $files;     /* Filesystem */
  private $command;   /* CrudScaffoldCommand */
  public $relations;
  public $json_data;
  public $models;
  public $use_laravel_auth;
  public $app_type;
  public $tool;

  /**
   * Create a new command instance.
   *
   * @param CrudScaffoldCommand $command
   * @param Filesystem $files
   */
  public function __construct(CrudScaffoldCommand $command, Filesystem $files)
  {
    $this->command = $command;
    $this->files = $files;
    $this->relations = [];
    $this->use_laravel_auth = false;
    $this->app_type = 'web';
    $this->tool = '';
    $this->json_data = null;
  }

  public function loadData()
  {

    $file_path = $this->command->argument('filePath');
    $this->command->info('reading setting file... (' . storage_path($file_path) . ')');

    //setting.json - ext check
    if (mb_substr(storage_path($file_path), -5) !== '.json') {
      $this->command->error('setting file must be json file (' . storage_path($file_path) . ')');
      exit();
    }

    // setting.json - exist check
    if (!$this->files->exists(storage_path($file_path))) {
      $this->command->error('setting file is not found (' . storage_path($file_path) . ')');
      exit();
    }

    //load file and delete comment
    try {
      $rawdata = $this->files->get(storage_path($file_path));
    } catch (FileNotFoundException $e) {
      throw new \Exception($e->getMessage());
    }
    $rawdata = preg_replace('#/\*[^\*]*\*/#', '', $rawdata);
    $this->json_data = json_decode($rawdata, true);

    //parse check
    if (!$this->json_data) {
      $this->command->error('json parse error! check your setting file  (' . storage_path($file_path) . ')');
      exit();
    } else {
      // try {
      $this->convertData();
      // } catch (\Exception $e) {
      //   throw new \Exception($e->getMessage());
      // }
    }
  }

  public function convertData()
  {
    $this->app_type = $this->json_data['app_type'];
    $this->use_laravel_auth = $this->json_data['use_laravel_auth'];
    $this->tool = $this->json_data['tool'];

    foreach ($this->json_data['models'] as $model) {
      $this->models[] = new Model($model);
    }

    // try {
    $this->prepareRelationship();
    // } catch (\Exception $e) {
    //   throw new \Exception();
    // }
  }

  public function prepareRelationship()
  {
    foreach ($this->models as $model) {
      echo('★---'.$model->name.' check start---★'."\n");
      if (!$model->is_pivot) {    // normal model
        foreach ($model->schemas as $schema) {
          if ($schema->belongsto === '') {
            continue;
          }

          $type = 'belongsTo';
          $originalModel = $model;
          $targetModel = $this->getModelByName($schema->belongsto);

          $relation = new Relation($type, $originalModel, $targetModel);
          $this->relations[] = $relation;
          $model->relations[] = $relation;

          $type = 'hasMany';
          try {
            $originalModel = $this->getModelByName($schema->belongsto);
          } catch (\Exception $e) {
            throw new \Exception();
          }
          $targetModel = $model;

          $relation = new Relation($type, $originalModel, $targetModel);
          $this->relations[] = $relation;
          // try {
          $this->getModelByName($schema->belongsto)->relations[] = $relation;
          // } catch (\Exception $e) {
          //   throw new \Exception();
          // }
        }
      } else {  // pivot model
        $type = 'belongsToMany';
        $originalModel = null;
        $targetModel = null;
        $pivotModel = $model;
        $pivotModelSchemas = [];
        foreach ($model->schemas as $schema) {
          if ($schema->belongsto === '') {
            $pivotModelSchemas[] = $schema;
          } else {
            if ($originalModel === null) {
              try {
                $originalModel = $this->getModelByName($schema->belongsto);
              } catch (\Exception $e) {
                throw new \Exception();
              }
            } else {
              try {
                $targetModel = $this->getModelByName($schema->belongsto);
              } catch (\Exception $e) {
                throw new \Exception();
              }
            }
          }
        }
        $relation = new Relation($type, $originalModel, $targetModel, $pivotModel, $pivotModelSchemas);
        $this->relations[] = $relation;
        $originalModel->relations[] = $relation;

        $relation = new Relation($type, $targetModel, $originalModel, $pivotModel, $pivotModelSchemas);
        $this->relations[] = $relation;
        $targetModel->relations[] = $relation;
      }
    }
  }

  public function getModelByName($name)
  {
    $result = array_filter($this->models, function ($model) use ($name) {
      return $model->name === $name;
    });
    if (count($result) === 0) {
      throw new \Exception('getModelByName(' . $name . ') return no model!');
    }
    return array_values($result)[0];
  }

}
