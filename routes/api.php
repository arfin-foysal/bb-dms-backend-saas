<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatagoryController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\api\GroupController;
use App\Http\Controllers\api\GroupFileController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\RoleHasPermissionController;
use App\Http\Controllers\Api\SubCatagoryController;
use App\Http\Controllers\Api\SubSubCatagoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserHasPermissionController;
use App\Http\Controllers\Api\UserHasRolesController;

use App\Models\Permission;

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

    //catagories
    Route::get('/category', [CatagoryController::class, 'index']);
    Route::post('/create_category', [CatagoryController::class, 'store']);
    Route::get('/category/{id}', [CatagoryController::class, 'show']);
    Route::put('/category/{id}', [CatagoryController::class, 'edit']);
    Route::post('/category_update/{id}', [CatagoryController::class, 'update']);
    Route::delete('/category/{id}', [CatagoryController::class, 'destroy']);
    Route::get('/category_all', [CatagoryController::class, 'allCategory']);
    Route::get('/category_show/{id}', [CatagoryController::class, 'showSubCatagory']);

    //subCatagoris
    Route::get('/sub_category_list', [SubCatagoryController::class, 'index']);
    Route::post('/create_sub_category', [SubCatagoryController::class, 'store']);
    Route::get('/sub_category/{id}', [SubCatagoryController::class, 'show']);
    Route::put('/sub_category/{id}', [SubCatagoryController::class, 'edit']);
    Route::post('/update_sub_category/{id}', [SubCatagoryController::class, 'update']);
    Route::delete('/delete_sub_category/{id}', [SubCatagoryController::class, 'destroy']);
    Route::get('/sub_category_show/{id}', [SubCatagoryController::class, 'showSubSubCatagory']);

    Route::get('/sub_category_by_category/{id}', [SubCatagoryController::class, 'subCategoryByCatagory']);

    //subSubCatagories
    Route::get('/sub_sub_category_list', [SubSubCatagoryController::class, 'index']);
    Route::post('/create_sub_sub_category', [SubSubCatagoryController::class, 'store']);
    Route::get('/sub_sub_category/{id}', [SubSubCatagoryController::class, 'show']);
    Route::put('/sub_sub_category/{id}', [SubSubCatagoryController::class, 'edit']);
    Route::post('/update_sub_sub_category/{id}', [SubSubCatagoryController::class, 'update']);

    Route::delete('/delete_sub_sub_category/{id}', [SubSubCatagoryController::class, 'destroy']);
    Route::get('/subsub-category-by-sub-category-id/{id}', [SubSubCatagoryController::class, 'getSubSubCatagoryBySubCatagoryId']);

    //document

    Route::get('/document', [DocumentController::class, 'index']);
    Route::post('/uploade-document', [DocumentController::class, 'store']);
    Route::get('/document/{id}', [DocumentController::class, 'show']);
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


    //show document


    Route::get('/sub-category-folder-by-category-id/{id}', [DocumentController::class, 'showSubCategory']);
    Route::get('/sub-category-documnet-by-sub-category-id/{id}', [DocumentController::class, 'showSubCategoryDocument']);
    Route::get('/third-sub-category-by-category-id/{id}', [DocumentController::class, 'showSubSubCategory']);

    Route::get('/third-sub-category-by-third-sub-category-id/{id}', [DocumentController::class, 'showSubSubCategoryDocument']);

    Route::get('/category-document-by-category-id/{id}', [DocumentController::class, 'showCategoryDocument']);

    Route::get('dashboard-details', [DocumentController::class, 'dashboardDetails']);

    //permission
    Route::get('/permission', [PermissionController::class, 'index']);

    //role
    Route::get('/role', [RoleController::class, 'index']);
    Route::get('/role/{id}', [RoleController::class, 'show']);
    Route::post('/role', [RoleController::class, 'store']);
    Route::post('/role/{id}', [RoleController::class, 'update']);

    //userHasPermission
    Route::post('/user_has_permission', [UserHasPermissionController::class, 'store']);
    Route::post('/user_has_permission/{id}', [UserHasPermissionController::class, 'update']);

    //roleHasPermission
    Route::post('/role_has_permission', [RoleHasPermissionController::class, 'store']);
    Route::post('/role_has_permission/{id}', [RoleHasPermissionController::class, 'update']);

    //userHasRole
    Route::post('/user_has_role', [UserHasRolesController::class, 'store']);

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
});

//Authentaction
Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);
Route::get('/document-view/{id}', [DocumentController::class, 'documentView']);
