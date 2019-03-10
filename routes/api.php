<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::namespace('Api')->group(function () {

    Route::post('auth/register', 'AuthController@register');
    Route::post('auth/login', 'AuthController@login');
    
    Route::group(['middleware' => 'jwt.auth'], function(){

        Route::get('auth/user', 'AuthController@user');
        Route::post('auth/logout', 'AuthController@logout');

        Route::get('/getusers', 'UsersController@index');
        Route::post('/getusers', 'UsersController@store');
        Route::get('/getusers/{id}', 'UsersController@edit');
        Route::post('/getusers/{id}', 'UsersController@update');
        Route::get('/getusers/destroy/{id}', 'UsersController@destroy');
        Route::post('/getusers/status/{id}', 'UsersController@statusUpdate');

        Route::get('/clients', 'ClientsController@index');
        Route::post('/clients', 'ClientsController@store');
        Route::get('/clients/{id}', 'ClientsController@edit');
        Route::post('/clients/{id}', 'ClientsController@update');
        Route::get('/clients/destroy/{id}', 'ClientsController@destroy');
        Route::post('/clients/status/{id}', 'ClientsController@statusUpdate');

        Route::get('/staff', 'StaffController@index');
        Route::post('/staff', 'StaffController@store');  
        Route::get('/staff/{id}', 'StaffController@edit');
        Route::post('/staff/{id}', 'StaffController@update');
        Route::get('/staff/destroy/{id}', 'StaffController@destroy');
        Route::post('/staff/status/{id}', 'StaffController@statusUpdate');

        Route::get('/certificate', 'CertificateController@index');
        Route::post('/certificate', 'CertificateController@store');  
        Route::get('/certificate/{id}', 'CertificateController@edit');
        Route::post('/certificate/{id}', 'CertificateController@update');
        Route::get('/certificate/destroy/{id}', 'CertificateController@destroy');
        Route::post('/certificate/status/{id}', 'CertificateController@statusUpdate');

        Route::get('/certificate_staff/{id}', 'CertificateStaffController@getStaffCertificates');
        Route::get('/certificate_staff/update/{id}', 'CertificateStaffController@updateStaffCertificates');

        Route::get('/city', 'CityController@index');
        Route::post('/city', 'CityController@store');  
        Route::get('/city/{id}', 'CityController@edit');
        Route::post('/city/{id}', 'CityController@update');
        Route::get('/city/destroy/{id}', 'CityController@destroy');
        Route::post('/city/status/{id}', 'CityController@statusUpdate');

        Route::get('/area', 'AreaController@index');
        Route::post('/area', 'AreaController@store');  
        Route::get('/area/{id}', 'AreaController@edit');
        Route::post('/area/{id}', 'AreaController@update');
        Route::get('/area/destroy/{id}', 'AreaController@destroy');
        Route::post('/area/status/{id}', 'AreaController@statusUpdate');

        Route::get('/supplier', 'SupplierController@index');
        Route::post('/supplier', 'SupplierController@store');
        Route::get('/supplier/{id}', 'SupplierController@edit');
        Route::post('/supplier/{id}', 'SupplierController@update');
        Route::get('/supplier/destroy/{id}', 'SupplierController@destroy');
        Route::post('/supplier/status/{id}', 'SupplierController@statusUpdate');  

        Route::get('/variation', 'VariationController@index');
        Route::post('/variation', 'VariationController@store');  
        Route::get('/variation/{id}', 'VariationController@edit');
        Route::post('/variation/{id}', 'VariationController@update');
        Route::get('/variation/destroy/{id}', 'VariationController@destroy');
        Route::post('/variation/status/{id}', 'VariationController@statusUpdate');  

        Route::get('/equipmenttype', 'EquipmenttypeController@index');
        Route::post('/equipmenttype', 'EquipmenttypeController@store');  
        Route::get('/equipmenttype/{id}', 'EquipmenttypeController@edit');
        Route::post('/equipmenttype/{id}', 'EquipmenttypeController@update');
        Route::get('/equipmenttype/destroy/{id}', 'EquipmenttypeController@destroy');
        Route::post('/equipmenttype/status/{id}', 'EquipmenttypeController@statusUpdate');  

        Route::get('/equipment', 'EquipmentController@index');
        Route::get('/equipment/getdata', 'EquipmentController@getdata');
        Route::post('/equipment', 'EquipmentController@store');
        Route::get('/equipment/{id}', 'EquipmentController@edit');
        Route::post('/equipment/{id}', 'EquipmentController@update');
        Route::get('/equipment/destroy/{id}', 'EquipmentController@destroy');
        Route::post('/equipment/status/{id}', 'EquipmentController@statusUpdate');  
      
        Route::get('/expensesupplier', 'ExpenseSupplierController@index');
        Route::post('/expensesupplier', 'ExpenseSupplierController@store');
        Route::get('/expensesupplier/{id}', 'ExpenseSupplierController@edit');
        Route::post('/expensesupplier/{id}', 'ExpenseSupplierController@update');
        Route::get('/expensesupplier/destroy/{id}', 'ExpenseSupplierController@destroy');
        Route::post('/expensesupplier/status/{id}', 'ExpenseSupplierController@statusUpdate');  
        
        Route::post('/bill', 'SupplierBillController@store');  
        Route::get('/bill', 'SupplierBillController@index');  
        Route::get('/bill/{id}', 'SupplierBillController@edit');  
        Route::post('/bill/status', 'SupplierBillController@statusUpdate');  
        Route::get('/getBills', 'SupplierBillController@byStatus');  
        Route::get('/getBills/multistatus', 'SupplierBillController@byMultiStatus');  

        Route::get('/expensetype', 'ExpensetypeController@index');
        Route::post('/expensetype', 'ExpensetypeController@store');  
        Route::post('/expensetype', 'ExpensetypeController@store');
        Route::get('/expensetype/{id}', 'ExpensetypeController@edit');
        Route::post('/expensetype/{id}', 'ExpensetypeController@update');
        Route::get('/expensetype/destroy/{id}', 'ExpensetypeController@destroy');
        Route::post('/expensetype/status/{id}', 'ExpensetypeController@statusUpdate');  

        Route::post('/expense/saveExpense_byId/{expensetype_id}', 'ExpenseController@saveExpense_byId');  
        Route::post('/expense/saveAllExpense/{dispatch_id}/{equipment_id}', 'ExpenseController@saveAllExpense');

        // Route::post('/fuel/saveFuel_byId/{expensetype_id}', 'ExpenseController@saveExpense_byId');  
        Route::post('/expense/saveAllFuel/{dispatch_id}/{equipment_id}', 'ExpenseController@saveAllFuel');  
        Route::get('/expense/getExpense', 'ExpenseController@getEquipmentExpense');  
        Route::get('/expense/getMultiExpense', 'ExpenseController@getEquipmentDisptachExpense');  
        Route::post('/expense/replaceEquipment', 'ExpenseController@replaceEquipment');  

        //Expense by supplier
        Route::post('/expense/saveAllFuelbySupplier/', 'ExpenseController@saveAllFuelbySupplier');  
        

        Route::get('/expense/getFuel', 'ExpenseController@getEquipmentFuel');  
        Route::get('/expense/getBillFuel', 'ExpenseController@getBillFuel');  
        Route::get('/expense/getApprovedFuelBill', 'ExpenseController@getApprovedFuelBill');  
        Route::get('/expense/getFuelByDate', 'ExpenseController@getFuelByDate');  
        Route::post('/expense/approveIt/{expense_id}/{bill_id}', 'ExpenseController@approveExpense');  
        Route::post('/expense/approveIt/{expense_id}', 'ExpenseController@approveExpense');  

        Route::post('/expense/savenapprove', 'ExpenseController@saveNapproveExpense');  

        Route::post('/expense/flagIt/{expense_id}', 'ExpenseController@flagExpense');  
        Route::post('/expense/deleteIt/{expense_id}', 'ExpenseController@deleteExpense');  

        Route::get('/searchExpense', 'ExpenseController@searchExpenseByEquipments');  
        Route::get('/expense/getExpenseCount', 'ExpenseController@getExpenseCount');  
        Route::get('/expense/flagged', 'ExpenseController@getFlagged');  
        Route::get('/expense/deleted', 'ExpenseController@getDeleted');  

        Route::post('/advanceSlip', 'AdvancepaymentsController@store');  

        Route::get('/dispatch', 'DispatchController@index');
        Route::get('/dispatch/getTrips', 'TripController@getAllByEquipment');  
        Route::get('/dispatch/getTripDates', 'TripController@getTripDates');  
        Route::get('/dispatch/getTripsByID', 'TripController@getAllByTripID'); 
        Route::get('/dispatch/getTripsByStatus', 'TripController@getTripsByStatus');  
        Route::post('/dispatch/updateTrips/{dispatch_id}', 'TripController@updateTrips');  
        Route::post('/dispatch', 'DispatchController@store');
        Route::get('/dispatch/{id}', 'DispatchController@edit');
        Route::post('/dispatch/{id}', 'DispatchController@update');
        Route::get('/dispatch/destroy/{id}', 'DispatchController@destroy');
        Route::post('/dispatch/status/{id}', 'DispatchController@statusUpdate');  
        Route::post('/dispatch/reserveEquipments/{id}', 'DispatchEquipmentController@store');  
        Route::post('/dispatch/storeTrips/{dispatchid}', 'TripController@store'); 

        Route::get('/getTripsByDate', 'AreaTripController@getTripsForDailyReport');
        Route::get('/searchTripByFields', 'AreaTripController@searchTripByFields');


        Route::get('/trip/getTrips', 'AreaTripController@getAllByEquipment');  
        Route::post('/trip/setBillT', 'TripController@setBillT');
        Route::post('/trip/replaceStaff', 'TripController@replaceStaff');
        Route::post('/trip/replaceEquipment/{equipment_id}/{dispatch_id}', 'TripController@replaceEquipment');

        Route::get('/getTripType', 'TripTypeController@index');  
        Route::get('/getEquipmentStatus/{dispatch_id}/{equipment_id}', 'DispatchEquipmentController@getEquipmentStatus');  
        Route::post('/trip/getAllTrips', 'AreaTripController@getAllTripByDispatch');  

        Route::post('/dispatchEquipment/ChangeEquipmentStatus/{dispatch_id}/{equipment_id}/{status}', 'DispatchEquipmentController@updateStatus');
    
        Route::get('/searchDispatch', 'DispatchController@searchDispatch');  

        Route::get('/getSalaryVariation', 'PresetSalaryVariationController@getVariations');  
        Route::post('/salaryVariation/saveVariations', 'SavedVariationController@store');  
        Route::get('/salaryVariation/getSavedVariations', 'SavedVariationController@getSavedVariations');  

        Route::get('/advancepayments/getByStaff', 'AdvancepaymentsController@getByStaff');  

        Route::post('/booking', 'BookingsController@store');
        Route::get('/getBookingsByDate', 'BookingsController@getBookingsByDate');
        Route::post('/moveSlot', 'BookingsController@moveSlot');
        

    });
      Route::group(['middleware' => 'jwt.refresh'], function(){
      Route::post('auth/refresh', 'AuthController@refresh');
    });

    

});

