<?php


namespace App\Console\Commands\CrudScaffold;


use Illuminate\Support\Str;

class Relation
{

  public $type;
  public $originalModel;
  public $targetModel;
  public $pivotModel;
  public $pivotModelSchemas;

  public function __construct($type, $originalModel, $targetModel, $pivotModel = null, $pivotModelSchemas = array())
  {
    $this->type = $type;   // belongsTo or hasMany or belongsToMany
    $this->originalModel = $originalModel;
    $this->targetModel = $targetModel;
    $this->pivotModel = $pivotModel;
    $this->pivotModelSchemas = $pivotModelSchemas;
  }

  public function implodePivotColumns()
  {
    $result = '';
    foreach ($this->pivotModelSchemas as $schema) {
      $result .= ",'" . Str::snake(Str::singular($schema->name)) . "'";
    }
    $result = ltrim($result, ',');
    return $result;
  }

}
