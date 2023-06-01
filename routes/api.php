<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatagoryController;
use App\Http\Controllers\Api\ClientInfoController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\GroupFileController;
use App\Http\Controllers\Api\SubCatagoryController;
use App\Http\Controllers\Api\SubSubCatagoryController;
use App\Http\Controllers\Api\UserController;

use Illuminate\Support\Facades\Auth;

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

// Route::get('storage-link', function () {
//     $targetFolder = public_path('app/public');
//     $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage';
//     symlink($linkFolder, $targetFolder);
//     return 'The [public/storage] folder has been linked';
// });




Route::group(['middleware' => ['auth:sanctum']], function () {

    // user api
    Route::get('/users-list', [UserController::class, 'index']);
    Route::post('/create-users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'edit']);
    Route::post('/users-update/{id}', [UserController::class, 'update']);
    Route::delete('/users-delete/{id}', [UserController::class, 'destroy']);
    Route::get('/all_user', [UserController::class, 'allUser']);
    Route::post('superadmin-create-or-update-and-company-assign', [UserController::class, 'superAdminCreateOrUpdateAndCompanyAssign']);
    Route::post('profile-update', [UserController::class, 'profileUpdate']);
    Route::post('password-change', [UserController::class, 'ChangePassword']);

    Route::get('/super-admin-list', [UserController::class, 'superAdminList']);
    Route::delete('/super-admin-delete/{id}', [UserController::class, 'superAdminDelete']);

    //categories
    Route::get('/category', [CatagoryController::class, 'index']);
    Route::post('/create_category', [CatagoryController::class, 'store']);
    Route::get('/category/{id}', [CatagoryController::class, 'show']);
    Route::put('/category/{id}', [CatagoryController::class, 'edit']);
    Route::post('/category_update/{id}', [CatagoryController::class, 'update']);
    Route::delete('/category/{id}', [CatagoryController::class, 'destroy']);
    Route::get('/category_all', [CatagoryController::class, 'allCategory']);
    Route::get('/category_show/{id}', [CatagoryController::class, 'showSubCatagory']);

    //subCategoris
    Route::get('/sub_category_list', [SubCatagoryController::class, 'index']);
    Route::post('/create_sub_category', [SubCatagoryController::class, 'store']);
    Route::get('/sub_category/{id}', [SubCatagoryController::class, 'show']);
    Route::put('/sub_category/{id}', [SubCatagoryController::class, 'edit']);
    Route::post('/update_sub_category/{id}', [SubCatagoryController::class, 'update']);
    Route::delete('/delete_sub_category/{id}', [SubCatagoryController::class, 'destroy']);
    Route::get('/sub_category_show/{id}', [SubCatagoryController::class, 'showSubSubCatagory']);
    Route::get('/sub_category_by_category/{id}', [SubCatagoryController::class, 'subCategoryByCatagory']);

    //subSubCategories
    Route::get('/sub_sub_category_list', [SubSubCatagoryController::class, 'index']);
    Route::post('/create_sub_sub_category', [SubSubCatagoryController::class, 'store']);
    Route::get('/sub_sub_category/{id}', [SubSubCatagoryController::class, 'show']);
    Route::put('/sub_sub_category/{id}', [SubSubCatagoryController::class, 'edit']);
    Route::post('/update_sub_sub_category/{id}', [SubSubCatagoryController::class, 'update']);
    Route::delete('/delete_sub_sub_category/{id}', [SubSubCatagoryController::class, 'destroy']);
    Route::get('/subsub-category-by-sub-category-id/{id}', [SubSubCatagoryController::class, 'getSubSubCatagoryBySubCatagoryId']);

    //document

    Route::get('/document', [DocumentController::class, 'allDocument']);
    Route::post('/uploade-document', [DocumentController::class, 'uploadeDocument']);
    Route::get('/document/{id}', [DocumentController::class, 'documnetByCategory']);
    Route::put('/document/{id}', [DocumentController::class, 'edit']);
    Route::post('/document/{id}', [DocumentController::class, 'update']);
    Route::delete('/document/{id}', [DocumentController::class, 'destroy']);
    Route::get('/document_show/{id}', [DocumentController::class, 'showDocument']);
    Route::get('/category_document/{id}', [DocumentController::class, 'showCategoryDocument']);
    Route::post('document_publish/{id}', [DocumentController::class, 'documentPublish']);
    Route::get('adminunpublish_document_list', [DocumentController::class, 'AdminUnpubishDocumentList']);
    Route::get('all_publish_document', [DocumentController::class, 'AllPublishDocument']);
    Route::get('your_document', [DocumentController::class, 'yourDocument']);
    Route::get('dashboard_Publish_Document', [DocumentController::class, 'dashboardPublishDocument']);
    Route::post('admin_document_publish/{id}', [DocumentController::class, 'AdminPublishDocument']);
    Route::get('admin-cancel-document/{id}', [DocumentController::class, 'AdminCancelPublishDocument']);


    //show document 


    Route::get('/sub-category-folder-by-category-id/{id}', [DocumentController::class, 'showSubCategory']);
    Route::get('/sub-category-documnet-by-sub-category-id/{id}', [DocumentController::class, 'showSubCategoryDocument']);
    Route::get('/third-sub-category-by-category-id/{id}', [DocumentController::class, 'showSubSubCategory']);
    Route::get('/third-sub-category-by-third-sub-category-id/{id}', [DocumentController::class, 'showSubSubCategoryDocument']);
    Route::get('/category-document-by-category-id/{id}', [DocumentController::class, 'showCategoryDocument']);
    Route::get('dashboard-details', [DocumentController::class, 'dashboardDetails']);
    Route::get('/document-view/{id}', [DocumentController::class, 'documentView']);

    //group
    Route::post('create_group', [GroupController::class, 'createGroup']);
    Route::post('group_update/{id}', [GroupController::class, 'updateGroup']);
    Route::get('get_singal_group/{id}', [GroupController::class, 'singalGroup']);
    Route::get('user_wise_group_view', [GroupController::class, 'userWiseGroupView']);
    Route::get('all_user_for_group', [UserController::class, 'allUserforGroup']);
    Route::delete('delete_group/{id}', [GroupController::class, 'destroyGroup']);

    //group document
    Route::post('create_group_documnet', [GroupFileController::class, 'createGroupDocumnent']);
    Route::post('group_documnet_update/{id}', [GroupFileController::class, 'documnetupdate']);
    Route::get('get_group_document/{id}', [GroupFileController::class, 'getGroupDocument']);
    Route::get('group_singal_document/{id}', [GroupFileController::class, 'groupSingalDocumnet']);
    Route::delete('delete_group_documnet/{id}', [GroupFileController::class, 'destroyGroupDocument']);
    Route::any('share_document', [GroupFileController::class, 'shareDocument']);
    Route::get('download/{id}', [DocumentController::class, 'download']);
    Route::get('download_file/{id}', [GroupFileController::class, 'downloadFile']);

    //company

    Route::post('create-or-update-company', [CompanyController::class, 'createOrUpdateCompany']);
    Route::get('company-list', [CompanyController::class, 'companyList']);
    Route::delete('delete-company/{id}', [CompanyController::class, 'companyDelete']);


//Client info
    Route::get('all-client-list', [ClientInfoController::class, 'allClientInfo']);
});
//Client info
Route::post('add-client-info', [ClientInfoController::class, 'addClientInfo']);
//Authentaction
Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);

// Route::get('test',function(){
//     $authId = Auth::user();
//     return  $authId;
// });
