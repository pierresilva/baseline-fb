<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::any('/test', function (Request $request) {

  return response()->json([
    'message' => 'Hello world!',
    'value' => 'My Quote!',
    'data' => $request->all()
  ]);
});

Route::post('/schema', function (Request $request) {
  $requestData = $request->all();
  if (!count($requestData['models'])) {
    return response()->json([
      'message' => 'No models found!'
    ], 422);
  }

  $fileContent = json_encode($requestData, JSON_PRETTY_PRINT);

  $fileContent = str_replace('null,', '"",', $fileContent);

  try {
    File::put(storage_path('crud-scaffold.json'), $fileContent);
  } catch (Exception $exception) {
    throw new Exception($exception->getMessage());
  }

  return response()->json([
    'message' => 'Schema saved successfully'
  ], 200);

});

Route::get('/schema', function (Request $request) {
  $schemaFile = File::get(storage_path('crud-scaffold.json'));
  return response()->json([
    'message' => 'Schema obtained successfully',
    'data' => json_decode($schemaFile),
  ], 200);
});

Route::get("users/{user}/duplicate", ['as' => 'users.duplicate', 'uses' => 'UserController@duplicate']);
Route::resource("users","UserController");

Route::get("companies/{company}/duplicate", ['as' => 'companies.duplicate', 'uses' => 'CompanyController@duplicate']);
Route::resource("companies","CompanyController");

Route::get("hobbies/{hobby}/duplicate", ['as' => 'hobbies.duplicate', 'uses' => 'HobbyController@duplicate']);
Route::resource("hobbies","HobbyController");

Route::get("dogs/{dog}/duplicate", ['as' => 'dogs.duplicate', 'uses' => 'DogController@duplicate']);
Route::resource("dogs","DogController");

