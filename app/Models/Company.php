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
    public static function getValidateRule(Company $company=null){
        if($company){
            $ignore_unique = $company->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'companies';
        $validation_rule = [

            'model.name' => 'required|unique:'.$table_name.',name,'.$ignore_unique.',id,deleted_at,NOT_NULL',


        ];
        if($company){

        }
        return $validation_rule;
    }

	public function users() {
		return $this->hasMany('App\User');
	}






	public static function getLists() {
		$lists = [];
		$lists['User'] = User::pluck( 'name' ,'id' );
		return $lists;
	}
}
