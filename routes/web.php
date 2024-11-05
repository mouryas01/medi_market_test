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
Route::redirect('/', '/login');
Auth::routes();
Route::get('logout', function(){
    Session::forget('uid');
    Session::forget('user_type');
})->name('logout');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/admin/home', 'HomeController@adminHome')->name('admin.home')->middleware('firebase');
Route::get('/profile', 'HomeController@profile')->name('get-profile');
Route::get('/profile/edit/{id}', 'HomeController@editProfile')->name('edit-profile');
Route::post('/updateprofile', 'HomeController@updateProfile')->name('update-profile');
Route::post('/change_password', 'HomeController@change_password')->name('change-password');

Route::get('/menus', 'HomeController@menus')->name('menus')->middleware('firebase');
Route::post('/menus', 'HomeController@setpermission')->name('setpermission')->middleware('firebase');

Route::post('/password/email', 'AccountsController@sendResetLinkEmail')->name('verify_password');
Route::post('/password/reset', 'AccountsController@reset_password')->name('reset_password');

Route::group(['prefix' => 'admin', 'middleware' => 'firebase'], function(){
    
    //Category    
    Route::resource("/categories", "CategoriesController")->only(["index","create","store","show","edit","update","destroy"]);

    //Notification Route
    Route::get("/send-nofiication", "NotificationController@index")->name("notification.index");
    Route::post("/send-nofiication", "NotificationController@sendNotification")->name("notification.send");


    //Enquiery Route
    Route::get("/enquiries", "EnquiryController@index")->name("enquiries");
    Route::post("/enquiries/reply", "EnquiryController@enquiry_reply")->name("enquiry.reply");
    Route::post("/enquiries/fetch", "EnquiryController@fetch_enquiry")->name("enquiry.fetch");

    //Users Route (Vendors)
    Route::resource("/users", "PeopleController")->only(["index","show"]);

    //Users Route (Vendors)
    Route::resource("/vendors", "VendorController")->only(["index","store","show","edit","update"]);
    
    //Event filter
    Route::post("/events/filterby", "EventController@eventFilterByDate")->name('eventFilterByDate');
    //Events
    Route::resource("/events", "EventController")->only(["index","create","store","show","edit","update","destroy"]);

    //Blogs
    Route::resource("/blogs", "BlogController")->only(["index","create","store","show","edit","update","destroy"]);

    //Promotion & eCoupan
    Route::resource("/promotions", "PromotionsController")->only(["index","create","store","show","edit","update","destroy"]);    
});


//Test category table
Route::get('/categories/category_table', 'CategoriesController@category_table')->name('category_table');
//Subcategory 
Route::post('/categories/subcategory', 'CategoriesController@getSubCategory')->name('subcategory');
Route::post('/categories/sub-subcategory', 'CategoriesController@getSubSubCategory')->name('sub-subcategory');
Route::post('/categories/delete', 'CategoriesController@deleteCategory')->name('category_delete');


//Products
Route::get('/products/importExport', 'ProductsController@importExport')->name('importexport');
Route::get('/products/downloadExcel/{type}', 'ProductsController@downloadExcel')->name('download');
Route::post('/products/importExcel', 'ProductsController@importExcel')->name('importexcel');
Route::post('/products/assignProduct', 'ProductsController@assignProduct')->name('assignproduct');
Route::post('/products/importImage', 'ProductsController@importImage')->name('importImage');
Route::post("/products/approve", "ProductsController@approve")->name('products.approve');
Route::post("/products/quentity", "ProductsController@updateQuentity")->name('products.quentity');
Route::resource("/products", "ProductsController")->only(["index","create","store","show","edit","update","destroy"]);

//Orders
Route::post("/orders/filterby", "OrdersController@orderFilterByDate")->name('orderFilterByDate');
Route::post("/orders/payment", "OrdersController@payment")->name('orders.payment');
Route::post("/orders/approve", "OrdersController@approve")->name('orders.approve');
Route::get("/orders/show/{cid}/{okey}", "OrdersController@show")->name('orders.show');
Route::resource("/orders", "OrdersController")->only(["index","store","edit","update"]);

//Stores
Route::resource("/stores", "StoreController")->only(["index","create","store","show","edit","update","destroy"]);

//Reports
Route::get('/reports/sales', 'ReportsController@salesReport')->name('sales-report');
Route::post('/reports/sales', 'ReportsController@salesReportFilterByDate')->name('salesReportFilterByDate');
Route::get('/reports/inventory', 'ReportsController@inventoryReport')->name('inventory-report');
Route::post('/reports/inventory', 'ReportsController@inventoryReportFilterByDate')->name('inventoryReportFilterByDate');
Route::get('/reports/return', 'ReportsController@returnReport')->name('return-report');
Route::post('/reports/return', 'ReportsController@returnReportFilterByDate')->name('returnReportFilterByDate');
