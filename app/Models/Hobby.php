<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Models\Hobby
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Hobby newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Hobby newQuery()
 * @method static \Illuminate\Database\Query\Builder|Hobby onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Hobby query()
 * @method static \Illuminate\Database\Eloquent\Builder|Hobby whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hobby whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hobby whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hobby whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hobby whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Hobby withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Hobby withoutTrashed()
 * @mixin \Eloquent
 */
class Hobby extends Model
{
  use SoftDeletes;


  // Mass Assignment
  protected $fillable = ['name',];
  protected $dates = ['deleted_at'];


  // Validate Rule
  public static function getValidateRule(Hobby $hobby = null)
  {
    if ($hobby) {
      $ignore_unique = $hobby->id;
    } else {
      $ignore_unique = 'NULL';
    }
    $table_name = 'hobbies';
    $validation_rule = [

      'model.name' => 'required',

      'pivots.user.*.skill_level' => 'integer|nullable',
      'pivots.user.*.firend_name' => 'required',

    ];
    if ($hobby) {

    }
    return $validation_rule;
  }


  public function users()
  {
    return $this->belongsToMany('App\User')
      ->withPivot('skill_level', 'firend_name')
      ->orderBy('id')
      ->withTimestamps();
  }


  public static function getLists()
  {
    $lists = [];
    $lists['User'] = User::pluck('name', 'id');
    return $lists;
  }
}
