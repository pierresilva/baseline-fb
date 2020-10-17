<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Models\Dog
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $user_id
 * @property string $name
 * @property int $weight
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Dog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dog newQuery()
 * @method static \Illuminate\Database\Query\Builder|Dog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Dog query()
 * @method static \Illuminate\Database\Eloquent\Builder|Dog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dog whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dog whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dog whereWeight($value)
 * @method static \Illuminate\Database\Query\Builder|Dog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Dog withoutTrashed()
 * @mixin \Eloquent
 */
class Dog extends Model
{
    use SoftDeletes;


	// Mass Assignment
	protected $fillable = ['user_id','name','weight',];
    protected $dates = ['deleted_at'];


	// Validate Rule
    public static function getValidateRule(Dog $dog=null){
        if($dog){
            $ignore_unique = $dog->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'dogs';
        $validation_rule = [

            'model.user_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.weight' => 'integer|required',


        ];
        if($dog){

        }
        return $validation_rule;
    }



	public function user() {
		return $this->belongsTo('App\User');
	}




	public static function getLists() {
		$lists = [];
		$lists['User'] = User::pluck( 'name' ,'id' );
		return $lists;
	}
}
