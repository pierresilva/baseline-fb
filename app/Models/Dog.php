<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


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
