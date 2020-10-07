<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Company extends Model
{
  use SoftDeletes;

  // Mass Assignment
  protected $fillable = ['name',];
  protected $dates = ['deleted_at'];

  // Validate Rule
  public static function getValidateRule(Company $company = null)
  {
    $ignoreUnique = null;

    if ($company) {
      $ignoreUnique = $company->id;
    }
    $tableName = 'companies';
    $validationRule = [

      'model.name' => 'required|unique:' . $tableName . ',name,' . $ignoreUnique . ',id,deleted_at,NOT_NULL',


    ];
    if ($company) {
    }
    return $validationRule;
  }

  public function users()
  {
    return $this->hasMany('App\User');
  }

  public static function getLists()
  {
    $lists = [];
    $lists['User'] = User::pluck('name', 'id');
    return $lists;
  }
}
