<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		$companies = new Company;

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

                        $companies = $companies->whereHas($collumn_name, function($q) use($related_column_name, $operator, $value){
    						$q->where( $related_column_name, $operator, $value );
                        });

                    }else{
                        $companies = $companies->where( $collumn_name, $operator, $value );
                    }
                }
            }
        }
        $companies = $companies->get();



        // (2)sort
        $q_s = $request->input('q.s');
        if($q_s){

            // sort dir and sort column
            if( substr( $q_s,-5,5 ) === '_desc' ){
                $sort_column = substr( $q_s, 0, strlen($q_s)-5 );
                $companies = $companies->sortByDesc($sort_column);
            }elseif( substr( $q_s,-4,4 ) === '_asc' ){
                $sort_column = substr( $q_s, 0, strlen($q_s)-4 );
                $companies = $companies->sortBy($sort_column);
            }

        }else{
            $companies = $companies->sortByDesc('id');
        }



        // (3)paginate
        $companies = $companies->paginate(10);

		return view('companies.index', compact('companies'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('companies.create')->with( 'lists', Company::getLists() );
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
		$company = Company::create( $input );

        //sync(attach/detach)
        if($request->input('pivots')){
            $this->sync($request->input('pivots'), $company);
        }

        DB::commit();

		return redirect()->route('companies.index')->with('message', 'Item created successfully.');
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Company  $company     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
		return view('companies.show', compact('company'));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $company     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
		return view('companies.edit', compact('company'))->with( 'lists', Company::getLists() );
    }



	/**
	 * Show the form for duplicatting the specified resource.
	 *
	 * @param \App\Company  $company	 * @return \Illuminate\Http\Response
	 */
	public function duplicate(Company $company)
	{
		return view('companies.duplicate', compact('company'))->with( 'lists', Company::getLists() );
	}



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $company     * @return \Illuminate\Http\Response
     */
    public function update(Company $company, Request $request)
    {
        $this->varidate($request, $company);

        $input = $request->input('model');

        DB::beginTransaction();


		//update data
		$company->update( $input );

        //sync(attach/detach)
        if($request->input('pivots')){
            $this->sync($request->input('pivots'), $company);
        }

        DB::commit();

		return redirect()->route('companies.index')->with('message', 'Item updated successfully.');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Company  $company     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
		$company->delete();
		return redirect()->route('companies.index')->with('message', 'Item deleted successfully.');
    }

    /**
     * Varidate input data.
     *
     * @return array
     */
    public function varidate(Request $request, Company $company = null)
    {
        $request->validate(Company::getValidateRule($company));
    }

    /**
     * sync pivot data
     *
     * @return void
     */
    public function sync($pivots_data, Company $company)
    {
        foreach( $pivots_data as $pivot_child_model_name => $pivots ){

            // remove 'id'
            foreach($pivots as &$value){
                if( array_key_exists('id', $value) ){
                    unset($value['id']);
                }
            }unset($value);

            $method = Str::camel( Str::plural($pivot_child_model_name) );
            $company->$method()->sync($pivots);
        }
    }
}
