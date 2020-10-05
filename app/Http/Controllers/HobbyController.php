<?php

namespace App\Http\Controllers;

use App\Models\Hobby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HobbyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		$hobbies = new Hobby;

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

                        $hobbies = $hobbies->whereHas($collumn_name, function($q) use($related_column_name, $operator, $value){
    						$q->where( $related_column_name, $operator, $value );
                        });

                    }else{
                        $hobbies = $hobbies->where( $collumn_name, $operator, $value );
                    }
                }
            }
        }
        $hobbies = $hobbies->get();



        // (2)sort
        $q_s = $request->input('q.s');
        if($q_s){

            // sort dir and sort column
            if( substr( $q_s,-5,5 ) === '_desc' ){
                $sort_column = substr( $q_s, 0, strlen($q_s)-5 );
                $hobbies = $hobbies->sortByDesc($sort_column);
            }elseif( substr( $q_s,-4,4 ) === '_asc' ){
                $sort_column = substr( $q_s, 0, strlen($q_s)-4 );
                $hobbies = $hobbies->sortBy($sort_column);
            }

        }else{
            $hobbies = $hobbies->sortByDesc('id');
        }



        // (3)paginate
        $hobbies = $hobbies->paginate(10);

		return view('hobbies.index', compact('hobbies'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('hobbies.create')->with( 'lists', Hobby::getLists() );
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
		$hobby = Hobby::create( $input );

        //sync(attach/detach)
        if($request->input('pivots')){
            $this->sync($request->input('pivots'), $hobby);
        }

        DB::commit();

		return redirect()->route('hobbies.index')->with('message', 'Item created successfully.');
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Hobby  $hobby     * @return \Illuminate\Http\Response
     */
    public function show(Hobby $hobby)
    {
		return view('hobbies.show', compact('hobby'));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Hobby  $hobby     * @return \Illuminate\Http\Response
     */
    public function edit(Hobby $hobby)
    {
		return view('hobbies.edit', compact('hobby'))->with( 'lists', Hobby::getLists() );
    }



	/**
	 * Show the form for duplicatting the specified resource.
	 *
	 * @param \App\Hobby  $hobby	 * @return \Illuminate\Http\Response
	 */
	public function duplicate(Hobby $hobby)
	{
		return view('hobbies.duplicate', compact('hobby'))->with( 'lists', Hobby::getLists() );
	}



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Hobby  $hobby     * @return \Illuminate\Http\Response
     */
    public function update(Hobby $hobby, Request $request)
    {
        $this->varidate($request, $hobby);

        $input = $request->input('model');

        DB::beginTransaction();


		//update data
		$hobby->update( $input );

        //sync(attach/detach)
        if($request->input('pivots')){
            $this->sync($request->input('pivots'), $hobby);
        }

        DB::commit();

		return redirect()->route('hobbies.index')->with('message', 'Item updated successfully.');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Hobby  $hobby     * @return \Illuminate\Http\Response
     */
    public function destroy(Hobby $hobby)
    {
		$hobby->delete();
		return redirect()->route('hobbies.index')->with('message', 'Item deleted successfully.');
    }

    /**
     * Varidate input data.
     *
     * @return array
     */
    public function varidate(Request $request, Hobby $hobby = null)
    {
        $request->validate(Hobby::getValidateRule($hobby));
    }

    /**
     * sync pivot data
     *
     * @return void
     */
    public function sync($pivots_data, Hobby $hobby)
    {
        foreach( $pivots_data as $pivot_child_model_name => $pivots ){

            // remove 'id'
            foreach($pivots as &$value){
                if( array_key_exists('id', $value) ){
                    unset($value['id']);
                }
            }unset($value);

            $method = Str::camel( Str::plural($pivot_child_model_name) );
            $hobby->$method()->sync($pivots);
        }
    }
}
