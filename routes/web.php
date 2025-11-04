<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentManualMutuController;
use App\Http\Controllers\QAQCController;
use App\Http\Controllers\SQAMCustomerController;
use App\Http\Controllers\SQAMSupplierController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CalenderAuditController;
use App\Http\Controllers\CustomerAuditController;
use App\Http\Controllers\DashboardDataClaimController;
use App\Http\Controllers\DataClaimController;
use App\Http\Controllers\ManagementRepresentativeController;
use App\Http\Controllers\UserController;

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
//     return view('welcome');
// });
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login-form');
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::group(
    ['middleware' => ['auth']],
    function () {
        Route::get('/filter-documents', [DashboardController::class, 'filterDocuments'])->name('filter.documents');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // manual-mutu
        Route::get('manual-mutu/count', [DocumentManualMutuController::class, 'count'])->name('manual-mutu.count');
        Route::resource('manual-mutu', DocumentManualMutuController::class);
        Route::post('manual-mutu/list', [DocumentManualMutuController::class, 'list'])->name('manual-mutu.list');
        Route::get('manual-mutu/{id}/revisi', [DocumentManualMutuController::class, 'revisi'])->name('manual-mutu.revisi');
        Route::post('manual-mutu/{id}/revisi', [DocumentManualMutuController::class, 'storeRevisi'])->name('manual-mutu.storeRevisi');

        Route::get('manual-mutu/revisi/all', [DocumentManualMutuController::class, 'allRevisions'])->name('manual-mutu.revisi-show');

        Route::get('sqam-customer/count', [SQAMCustomerController::class, 'count'])->name('sqam-customer.count');
        Route::resource('sqam-customer', SQAMCustomerController::class);
        Route::post('sqam-customer/list', [SQAMCustomerController::class, 'list'])->name('sqam-customer.list');
        Route::get('sqam-customer/{id}/revisi', [SQAMCustomerController::class, 'revisi'])->name('sqam-customer.revisi');
        Route::post('sqam-customer/{id}/revisi', [SQAMCustomerController::class, 'storeRevisi'])->name('sqam-customer.storeRevisi');

        Route::get('sqam-customer/revisi/all', [SQAMCustomerController::class, 'allRevisions'])->name('sqam-customer.revisi-show');

        //sqam-supplier
        Route::get('sqam-supplier/count', [SQAMSupplierController::class, 'count'])->name('sqam-supplier.count');
        Route::resource('sqam-supplier', SQAMSupplierController::class);
        Route::post('sqam-supplier/list', [SQAMSupplierController::class, 'list'])->name('sqam-supplier.list');
        Route::get('sqam-supplier/{id}/revisi', [SQAMSupplierController::class, 'revisi'])->name('sqam-supplier.revisi');
        Route::post('sqam-supplier/{id}/revisi', [SQAMSupplierController::class, 'storeRevisi'])->name('sqam-supplier.storeRevisi');

        Route::get('sqam-supplier/revisi/all', [SQAMSupplierController::class, 'allRevisions'])->name('sqam-supplier.revisi-show');

        Route::prefix('docs-quality-sop')->group(function () {
            // qa-qc
            Route::get('qa-qc/count-sop', [QAQCController::class, 'countSop'])->name('qa-qc.count-sop');
            Route::get('qa-qc/count-wi', [QAQCController::class, 'countWi'])->name('qa-qc.count-wi');
            Route::get('qa-qc/count-form', [QAQCController::class, 'countForm'])->name('qa-qc.count-form');
            Route::get('qa-qc/count-all', [QAQCController::class, 'countAll'])->name('qa-qc.count-all');
            Route::get('qa-qc', [QAQCController::class, 'index'])->name('qa-qc.index');
            Route::get('qa-qc/sop-list-partial', [QAQCController::class, 'contentListPartial'])->name('qa-qc.content-list-partial');
            Route::post('qa-qc/store-sop', [QAQCController::class, 'storeSop'])->name('qa-qc.store-sop');
            Route::get('qa-qc/{sop}/edit-sop', [QAQCController::class, 'editSop'])->name('qa-qc.edit-sop');
            Route::put('qa-qc/{sop}/update-sop', [QAQCController::class, 'updateSop'])->name('qa-qc.update-sop');
            Route::delete('qa-qc/{sop}/destroy-sop', [QAQCController::class, 'destroySop'])->name('qa-qc.destroy-sop');
            Route::get('qa-qc/{sop}/revisi-sop-data', [QAQCController::class, 'revisiSOP'])->name('qa-qc.revisi-sop');
            Route::post('qa-qc/{sop}/revisi-sop', [QAQCController::class, 'reviseSOP'])->name('qa-qc.revise-sop');

            Route::post('qa-qc/{sop}/store-wi', [QAQCController::class, 'storeWI'])->name('qa-qc.store-wi');
            Route::get('qa-qc/{wi}/edit-wi', [QAQCController::class, 'editWI'])->name('qa-qc.edit-wi');
            Route::put('qa-qc/{wi}/update-wi', [QAQCController::class, 'updateWI'])->name('qa-qc.update-wi');
            Route::delete('qa-qc/{wi}/destroy-wi', [QAQCController::class, 'destroyWI'])->name('qa-qc.destroy-wi');
            Route::get('qa-qc/{wi}/revisi-wi-data', [QAQCController::class, 'revisiWI'])->name('qa-qc.revisi-wi');
            Route::post('qa-qc/{wi}/revisi-wi', [QAQCController::class, 'reviseWI'])->name('qa-qc.revise-wi');

            Route::post('qa-qc/{wi}/store-form', [QAQCController::class, 'storeForm'])->name('qa-qc.store-form');
            Route::get('qa-qc/{form}/edit-form', [QAQCController::class, 'editForm'])->name('qa-qc.edit-form');
            Route::put('qa-qc/{form}/update-form', [QAQCController::class, 'updateForm'])->name('qa-qc.update-form');
            Route::delete('qa-qc/{form}/destroy-form', [QAQCController::class, 'destroyForm'])->name('qa-qc.destroy-form');
            Route::get('qa-qc/{form}/revisi-form-data', [QAQCController::class, 'revisiForm'])->name('qa-qc.revisi-form');
            Route::post('qa-qc/{form}/revisi-form', [QAQCController::class, 'reviseForm'])->name('qa-qc.revise-form');
            Route::get('qa-qc/revisi-all', [QAQCController::class, 'showAllRevisions'])->name('qa-qc.revisi-show');

            // management-representative
            Route::get('management-representative/count-sop', [ManagementRepresentativeController::class, 'countSop'])->name('management-representative.count-sop');
            Route::get('management-representative/count-wi', [ManagementRepresentativeController::class, 'countWi'])->name('management-representative.count-wi');
            Route::get('management-representative/count-form', [ManagementRepresentativeController::class, 'countForm'])->name('management-representative.count-form');
            Route::get('management-representative/count-all', [ManagementRepresentativeController::class, 'countAll'])->name('management-representative.count-all');
            Route::get('management-representative', [ManagementRepresentativeController::class, 'index'])->name('management-representative.index');
            Route::get('management-representative/sop-list-partial', [ManagementRepresentativeController::class, 'contentListPartial'])->name('management-representative.content-list-partial');

            Route::post('management-representative/store-sop', [ManagementRepresentativeController::class, 'storeSop'])->name('management-representative.store-sop');
            Route::get('management-representative/{sop}/edit-sop', [ManagementRepresentativeController::class, 'editSop'])->name('management-representative.edit-sop');
            Route::put('management-representative/{sop}/update-sop', [ManagementRepresentativeController::class, 'updateSop'])->name('management-representative.update-sop');
            Route::delete('management-representative/{sop}/destroy-sop', [ManagementRepresentativeController::class, 'destroySop'])->name('management-representative.destroy-sop');
            Route::get('management-representative/{sop}/revisi-sop-data', [ManagementRepresentativeController::class, 'revisiSOP'])->name('management-representative.revisi-sop');
            Route::post('management-representative/{sop}/revisi-sop', [ManagementRepresentativeController::class, 'reviseSOP'])->name('management-representative.revise-sop');

            Route::post('management-representative/{sop}/store-wi', [ManagementRepresentativeController::class, 'storeWI'])->name('management-representative.store-wi');
            Route::get('management-representative/{wi}/edit-wi', [ManagementRepresentativeController::class, 'editWI'])->name('management-representative.edit-wi');
            Route::put('management-representative/{wi}/update-wi', [ManagementRepresentativeController::class, 'updateWI'])->name('management-representative.update-wi');
            Route::delete('management-representative/{wi}/destroy-wi', [ManagementRepresentativeController::class, 'destroyWI'])->name('management-representative.destroy-wi');
            Route::get('management-representative/{wi}/revisi-wi-data', [ManagementRepresentativeController::class, 'revisiWI'])->name('management-representative.revisi-wi');
            Route::post('management-representative/{wi}/revisi-wi', [ManagementRepresentativeController::class, 'reviseWI'])->name('management-representative.revise-wi');

            Route::post('management-representative/{wi}/store-form', [ManagementRepresentativeController::class, 'storeForm'])->name('management-representative.store-form');
            Route::get('management-representative/{form}/edit-form', [ManagementRepresentativeController::class, 'editForm'])->name('management-representative.edit-form');
            Route::put('management-representative/{form}/update-form', [ManagementRepresentativeController::class, 'updateForm'])->name('management-representative.update-form');
            Route::delete('management-representative/{form}/destroy-form', [ManagementRepresentativeController::class, 'destroyForm'])->name('management-representative.destroy-form');
            Route::get('management-representative/{form}/revisi-form-data', [ManagementRepresentativeController::class, 'revisiForm'])->name('management-representative.revisi-form');
            Route::post('management-representative/{form}/revisi-form', [ManagementRepresentativeController::class, 'reviseForm'])->name('management-representative.revise-form');
            Route::get('management-representative/revisi-all', [ManagementRepresentativeController::class, 'showAllRevisions'])->name('management-representative.revisi-show');
        });

        Route::prefix('claim-customer')->group(function () {
            Route::get('dashboard/data-claim', [DashboardDataClaimController::class, 'index'])->name('dashboard.data-claim');
            Route::get('dashboard/data-claim/list', [DashboardDataClaimController::class, 'list'])->name('dashboard.data-claim.list');
            Route::resource('data-claim', DataClaimController::class);
            Route::post('data-claim/list', [DataClaimController::class, 'list'])->name('data-claim.list');
        });

        Route::prefix('customer-audit')->group(function () {
            Route::get('refresh', [CalenderAuditController::class, 'refresh'])->name('customer-audit.refresh');
            Route::get('calender/{id}/detail', [CalenderAuditController::class, 'getEventDetail1'])->name('customer-audit.detail');

            Route::get('calender', [CalenderAuditController::class, 'index'])->name('calender.index');

            Route::resource('customer-audit', CustomerAuditController::class);
            Route::post('customer-audit/list', [CustomerAuditController::class, 'list'])->name('customer-audit.list');
        });

        Route::prefix('users-management')->group(function () {
            Route::resource('users', UserController::class);
            Route::post('users/list', [UserController::class, 'list'])->name('users.list');
        });
    }
);
