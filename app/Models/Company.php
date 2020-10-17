<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Models\Company
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company newQuery()
 * @method static \Illuminate\Database\Query\Builder|Company onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Company query()
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Company withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Company withoutTrashed()
 * @mixin \Eloquent
 */
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
