<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use pierresilva\AccessControl\Traits\AccessControlTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\pierresilva\AccessControl\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\pierresilva\AccessControl\Models\Role[] $roles
 * @property-read int|null $roles_count
 */

































class User extends Authenticatable implements JWTSubject
{
    use AccessControlTrait, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',
            'model.company_id' => 'integer|nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',
            'model.company_id' => 'integer|nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',
            'model.company_id' => 'integer|nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',
            'model.company_id' => 'integer|nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',
            'model.company_id' => 'integer|nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',
            'model.company_id' => 'integer|nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',
            'model.company_id' => 'integer|nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',
            'model.company_id' => 'integer|nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',
            'model.company_id' => 'integer|nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',
            'model.company_id' => 'integer|nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',
            'model.company_id' => 'integer|nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',
            'model.company_id' => 'integer|nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',
            'model.company_id' => 'integer|nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',
            'model.company_id' => 'integer|nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',
            'model.company_id' => 'integer|nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
	// Validate Rule
    public static function getValidateRule(User $user=null){
        if($user){
            $ignore_unique = $user->id;
        }else{
            $ignore_unique = 'NULL';
        }
        $table_name = 'users';
        $validation_rule = [

            'model.company_id' => 'integer|nullable',
            'model.name' => 'required',
            'model.email' => 'required|unique:'.$table_name.',email,'.$ignore_unique.',id',
            'model.password' => 'confirmed|required',
            'model.birth_day' => 'nullable',
            'model.phone' => 'nullable',

        	'pivots.hobby.*.skill_level' => 'integer|nullable',
        	'pivots.hobby.*.firend_name' => 'required',

        ];
        if($user){
            $validation_rule['model.password'] = str_replace( 'required', '', $validation_rule['model.password'] );
            $validation_rule['model.password'] = str_replace( '||', '|', $validation_rule['model.password'] );

        }
        return $validation_rule;
    }

	public function dogs() {
		return $this->hasMany('App\Dog');
	}


	public function company() {
		return $this->belongsTo('App\Company');
	}


	public function hobbies() {
		return $this->belongsToMany('App\Hobby')
		->withPivot('skill_level','firend_name')
		->orderBy('id')
		->withTimestamps();
	}


	public static function getLists() {
		$lists = [];
		$lists['Company'] = Company::pluck( 'name' ,'id' );
		$lists['Dog'] = Dog::pluck( 'name' ,'id' );
		$lists['Hobby'] = Hobby::pluck( 'name' ,'id' );
		return $lists;
	}
}
