<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Hobby extends Model
{
    use SoftDeletes;


	// Mass Assignment
	protected $fillable = ['name',];
    protected $dates = ['deleted_at'];


	// Validate Rule
    public static function getValidateRule(Hobby $hobby=null){
        if($hobby){
            $ignore_unique = $hobby->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'hobbies';
        $validation_rule = [

            'model.name' => 'required',

        	'pivots.user.*.skill_level' => 'integer|nullable',
        	'pivots.user.*.firend_name' => 'required',

        ];
        if($hobby){

        }
        return $validation_rule;
    }





	public function users() {
		return $this->belongsToMany('App\User')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['User'] = User::pluck( 'name' ,'id' );
		return $lists;
	}
}
