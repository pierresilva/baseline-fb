<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		$users = new User;

        // (1)filltering
        if( is_array($request->input('q')) ){

            foreach( $request->input('q') as $key => $value ){

                if($key !== 's'){

                    $pettern = '#([^\.]*)\.?([^\.]*)_([^\._]*)#u';
                    preg_match($pettern,$key,$m);

                    $collumn_name = $m[1];
                    $related_column_name = $m[2];

                    if($m[3] == 'eq'){
                        $operator = '=';
                    }elseif($m[3] == 'cont'){
                        $operator = 'like';
                        $value = '%'.$value.'%';
                    }elseif($m[3] == 'gt'){
                        $operator = '>=';
                    }elseif($m[3] == 'lt'){
                        $operator = '<=';
                    }

                    if( $related_column_name !== '' ){  // search at related table column

                        $users = $users->whereHas($collumn_name, function($q) use($related_column_name, $operator, $value){
    						$q->where( $related_column_name, $operator, $value );
                        });

                    }else{
                        $users = $users->where( $collumn_name, $operator, $value );
                    }
                }
            }
        }
        $users = $users->get();



        // (2)sort
        $q_s = $request->input('q.s');
        if($q_s){

            // sort dir and sort column
            if( substr( $q_s,-5,5 ) === '_desc' ){
                $sort_column = substr( $q_s, 0, strlen($q_s)-5 );
                $users = $users->sortByDesc($sort_column);
            }elseif( substr( $q_s,-4,4 ) === '_asc' ){
                $sort_column = substr( $q_s, 0, strlen($q_s)-4 );
                $users = $users->sortBy($sort_column);
            }

        }else{
            $users = $users->sortByDesc('id');
        }



        // (3)paginate
        $users = $users->paginate(10);

		return view('users.index', compact('users'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create')->with( 'lists', User::getLists() );
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->varidate($request);

        $input = $request->input('model');

        DB::beginTransaction();

        $input['password'] = bcrypt($input['password']);

		//create data
		$user = User::create( $input );

        //sync(attach/detach)
        if($request->input('pivots')){
            $this->sync($request->input('pivots'), $user);
        }

        DB::commit();

		return redirect()->route('users.index')->with('message', 'Item created successfully.');
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
		return view('users.show', compact('user'));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
		return view('users.edit', compact('user'))->with( 'lists', User::getLists() );
    }



	/**
	 * Show the form for duplicatting the specified resource.
	 *
	 * @param \App\User  $user	 * @return \Illuminate\Http\Response
	 */
	public function duplicate(User $user)
	{
		return view('users.duplicate', compact('user'))->with( 'lists', User::getLists() );
	}



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user     * @return \Illuminate\Http\Response
     */
    public function update(User $user, Request $request)
    {
        $this->varidate($request, $user);

        $input = $request->input('model');

        DB::beginTransaction();

        if( $input['password'] === '' ){
            $input['password'] = $user->password;
        }else{
            $input['password'] = bcrypt($input['password']);
        }

		//update data
		$user->update( $input );

        //sync(attach/detach)
        if($request->input('pivots')){
            $this->sync($request->input('pivots'), $user);
        }

        DB::commit();

		return redirect()->route('users.index')->with('message', 'Item updated successfully.');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
		$user->delete();
		return redirect()->route('users.index')->with('message', 'Item deleted successfully.');
    }

    /**
     * Varidate input data.
     *
     * @return array
     */
    public function varidate(Request $request, User $user = null)
    {
        $request->validate(User::getValidateRule($user));
    }

    /**
     * sync pivot data
     *
     * @return void
     */
    public function sync($pivots_data, User $user)
    {
        foreach( $pivots_data as $pivot_child_model_name => $pivots ){

            // remove 'id'
            foreach($pivots as &$value){
                if( array_key_exists('id', $value) ){
                    unset($value['id']);
                }
            }unset($value);

            $method = Str::camel( Str::plural($pivot_child_model_name) );
            $user->$method()->sync($pivots);
        }
    }
}
