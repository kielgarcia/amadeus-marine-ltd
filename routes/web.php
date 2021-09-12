<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     if(Auth::check()){
//         return view('/home');
//     }else{
//         return view('auth/login');
//     }
// });


Route::get('/', ['middleware' =>'guest', function(){
    return view('auth.login');
}]);

Route::get('permission:cache-reset', function () {
    Artisan::call('permission:cache-reset');

    return redirect()->back()->with('success',"User's role & permission refreshed.");
})->name('RolePermissionRefresh');


Auth::routes(['register' => false]);

Route::post('sendResetPasswordLink', 'Auth\ForgotPasswordController@sendResetPasswordLink')->name('sendResetPasswordLink');
Route::post('resetPassword', 'Auth\ResetPasswordController@resetPassword')->name('resetPassword');

Route::group(['middleware' => ['auth']], function() {
    
    
    Route::get('/home', 'HomeController@index')->name('home');

    // Admin Routes 

    Route::get('/activity-logs','AdminController@viewActivityLog')->name('viewActivityLog'); // View Activity Log Page

    Route::post('/change-password','AdminController@changeMyPassword')->name('changeMyPassword'); // Change password

    Route::get('/administration','AdminController@index')->name('viewAdministration'); // View User Management Page

    Route::post('/administration/saveUser','AdminController@saveUser')->name('saveUser'); // Save New User
    Route::post('/administration/updateUser','AdminController@updateUser')->name('updateUser'); // Update User
    Route::post('/administration/resetPassword','AdminController@resetUserPassword')->name('resetUserPassword'); // Reset User's Password

    Route::get('/get_permissions/{role_id}','AdminController@getPermissions'); // AJAX get role permissions
    Route::post('/administration/saveRole','AdminController@saveRole')->name('saveRole'); // Save New Role
    Route::post('/administration/updateRole','AdminController@updateRole')->name('updateRole'); // Update Role

    Route::post('/administration/savePermission','AdminController@savePermission')->name('savePermission'); // Save New Permission
    Route::post('/administration/updatePermission','AdminController@updatePermission')->name('updatePermission'); // Update Permission
    
    // Hull Routes
    Route::get('/hulls','HullController@index')->name('viewHulls'); // View Hull Main index
    Route::post('/drawings/saveShipBuildingHull','HullController@saveSbHull')->name('saveSbHull'); // Save Ship Building Hull
    Route::post('/drawings/deleteShipBuildingHull','HullController@deleteSbHull')->name('deleteSbHull'); // Delete Ship Building Hull
    Route::post('/drawings/updateShipBuildingHull','HullController@updateSbHull')->name('updateSbHull'); // Update Ship Building Hull

    // Drawing Routes
    
    Route::get('/drawings','DrawingController@index')->name('viewDrawings'); // View Drawings Main index
    
    Route::get('/download/{filename}','DrawingController@downloadDrawings')->name('download'); // Download drawings
    Route::get('/download_revised/{filename}','DrawingController@downloadRevised')->name('download_revised'); // Download Revision history drawings
    
    Route::get('/drawings/getSbHullDrawings/{selectedSbHullId}','DrawingController@getSbHullDrawings')->name('viewSbDrawing'); // Get drawings based on selected hull
    Route::get('/drawings/getRevisionHistory/{sbDrawingId}','DrawingController@getRevisionHistory'); // Get drawings revision history
    
    Route::post('/drawings/saveShipBuildingDrawing','DrawingController@saveSbDrawing')->name('saveSbDrawing'); // Save Ship Building Drawing
    Route::post('/drawings/updateShipBuildingDrawing','DrawingController@updateSbDrawing')->name('updateSbDrawing'); // Update Ship Building Drawing
    Route::post('/drawings/uploadRevisionShipBuildingDrawing','DrawingController@uploadSbDrawingRevision')->name('uploadSbDrawingRevision'); // Update Ship Building Drawing
    Route::post('/drawings/deleteDrawing','DrawingController@deleteDrawing')->name('deleteDrawing'); // Delete Drawing

    Route::post('/drawings/saveWipDrawing','DrawingController@saveWipDrawing')->name('saveWipDrawing'); // Save WIP Drawing
    Route::post('/drawings/updateWipDrawing','DrawingController@updateWipDrawing')->name('updateWipDrawing'); // Save WIP Drawing
    Route::post('/drawings/finalizeWipDrawing','DrawingController@finalizeWipDrawing')->name('finalizeWipDrawing'); // Finalize WIP Drawing
    
    // Certificate Routes
    Route::get('/certificates','DrawingController@viewCertificate')->name('viewCertificates'); // View Certificate index
    Route::get('/certificates/getHullCertificates/{selectedCertHullId}','DrawingController@getHullCertificates'); // Get certificates based on selected hull

    Route::post('/certificates/saveCertificate','DrawingController@saveCertificate')->name('saveCertificate'); // Save Certificate
    Route::post('/certificates/updateCertificate','DrawingController@updateCertificate')->name('updateCertificate'); // Save Certificate
});
