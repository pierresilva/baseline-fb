<?php

namespace App\Http\Controllers;

use App\Models\Dog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		$dogs = new Dog;

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

                        $dogs = $dogs->whereHas($collumn_name, function($q) use($related_column_name, $operator, $value){
    						$q->where( $related_column_name, $operator, $value );
                        });

                    }else{
                        $dogs = $dogs->where( $collumn_name, $operator, $value );
                    }
                }
            }
        }
        $dogs = $dogs->get();



        // (2)sort
        $q_s = $request->input('q.s');
        if($q_s){

            // sort dir and sort column
            if( substr( $q_s,-5,5 ) === '_desc' ){
                $sort_column = substr( $q_s, 0, strlen($q_s)-5 );
                $dogs = $dogs->sortByDesc($sort_column);
            }elseif( substr( $q_s,-4,4 ) === '_asc' ){
                $sort_column = substr( $q_s, 0, strlen($q_s)-4 );
                $dogs = $dogs->sortBy($sort_column);
            }

        }else{
            $dogs = $dogs->sortByDesc('id');
        }



        // (3)paginate
        $dogs = $dogs->paginate(10);

		return view('dogs.index', compact('dogs'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dogs.create')->with( 'lists', Dog::getLists() );
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


		//create data
		$dog = Dog::create( $input );

        //sync(attach/detach)
        if($request->input('pivots')){
            $this->sync($request->input('pivots'), $dog);
        }

        DB::commit();

		return redirect()->route('dogs.index')->with('message', 'Item created successfully.');
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Dog  $dog     * @return \Illuminate\Http\Response
     */
    public function show(Dog $dog)
    {
		return view('dogs.show', compact('dog'));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Dog  $dog     * @return \Illuminate\Http\Response
     */
    public function edit(Dog $dog)
    {
		return view('dogs.edit', compact('dog'))->with( 'lists', Dog::getLists() );
    }



	/**
	 * Show the form for duplicatting the specified resource.
	 *
	 * @param \App\Dog  $dog	 * @return \Illuminate\Http\Response
	 */
	public function duplicate(Dog $dog)
	{
		return view('dogs.duplicate', compact('dog'))->with( 'lists', Dog::getLists() );
	}



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dog  $dog     * @return \Illuminate\Http\Response
     */
    public function update(Dog $dog, Request $request)
    {
        $this->varidate($request, $dog);

        $input = $request->input('model');

        DB::beginTransaction();


		//update data
		$dog->update( $input );

        //sync(attach/detach)
        if($request->input('pivots')){
            $this->sync($request->input('pivots'), $dog);
        }

        DB::commit();

		return redirect()->route('dogs.index')->with('message', 'Item updated successfully.');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dog  $dog     * @return \Illuminate\Http\Response
     */
    public function destroy(Dog $dog)
    {
		$dog->delete();
		return redirect()->route('dogs.index')->with('message', 'Item deleted successfully.');
    }

    /**
     * Varidate input data.
     *
     * @return array
     */
    public function varidate(Request $request, Dog $dog = null)
    {
        $request->validate(Dog::getValidateRule($dog));
    }

    /**
     * sync pivot data
     *
     * @return void
     */
    public function sync($pivots_data, Dog $dog)
    {
        foreach( $pivots_data as $pivot_child_model_name => $pivots ){

            // remove 'id'
            foreach($pivots as &$value){
                if( array_key_exists('id', $value) ){
                    unset($value['id']);
                }
            }unset($value);

            $method = Str::camel( Str::plural($pivot_child_model_name) );
            $dog->$method()->sync($pivots);
        }
    }
}
