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
use App\Http\Controllers\EngineeringController;
use App\Http\Controllers\HumanCapitalController;
use App\Http\Controllers\IRGAController;
use App\Http\Controllers\MaintananceController;
use App\Http\Controllers\ManagementRepresentativeController;
use App\Http\Controllers\PPICController;
use App\Http\Controllers\SHEController;
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

            // ppic
            Route::get('ppic/count-sop', [PPICController::class, 'countSop'])->name('ppic.count-sop');
            Route::get('ppic/count-wi', [PPICController::class, 'countWi'])->name('ppic.count-wi');
            Route::get('ppic/count-form', [PPICController::class, 'countForm'])->name('ppic.count-form');
            Route::get('ppic/count-all', [PPICController::class, 'countAll'])->name('ppic.count-all');
            Route::get('ppic', [PPICController::class, 'index'])->name('ppic.index');
            Route::get('ppic/sop-list-partial', [PPICController::class, 'contentListPartial'])->name('ppic.content-list-partial');

            Route::post('ppic/store-sop', [PPICController::class, 'storeSop'])->name('ppic.store-sop');
            Route::get('ppic/{sop}/edit-sop', [PPICController::class, 'editSop'])->name('ppic.edit-sop');
            Route::put('ppic/{sop}/update-sop', [PPICController::class, 'updateSop'])->name('ppic.update-sop');
            Route::delete('ppic/{sop}/destroy-sop', [PPICController::class, 'destroySop'])->name('ppic.destroy-sop');
            Route::get('ppic/{sop}/revisi-sop-data', [PPICController::class, 'revisiSOP'])->name('ppic.revisi-sop');
            Route::post('ppic/{sop}/revisi-sop', [PPICController::class, 'reviseSOP'])->name('ppic.revise-sop');

            Route::post('ppic/{sop}/store-wi', [PPICController::class, 'storeWI'])->name('ppic.store-wi');
            Route::get('ppic/{wi}/edit-wi', [PPICController::class, 'editWI'])->name('ppic.edit-wi');
            Route::put('ppic/{wi}/update-wi', [PPICController::class, 'updateWI'])->name('ppic.update-wi');
            Route::delete('ppic/{wi}/destroy-wi', [PPICController::class, 'destroyWI'])->name('ppic.destroy-wi');
            Route::get('ppic/{wi}/revisi-wi-data', [PPICController::class, 'revisiWI'])->name('ppic.revisi-wi');
            Route::post('ppic/{wi}/revisi-wi', [PPICController::class, 'reviseWI'])->name('ppic.revise-wi');

            Route::post('ppic/{wi}/store-form', [PPICController::class, 'storeForm'])->name('ppic.store-form');
            Route::get('ppic/{form}/edit-form', [PPICController::class, 'editForm'])->name('ppic.edit-form');
            Route::put('ppic/{form}/update-form', [PPICController::class, 'updateForm'])->name('ppic.update-form');
            Route::delete('ppic/{form}/destroy-form', [PPICController::class, 'destroyForm'])->name('ppic.destroy-form');
            Route::get('ppic/{form}/revisi-form-data', [PPICController::class, 'revisiForm'])->name('ppic.revisi-form');
            Route::post('ppic/{form}/revisi-form', [PPICController::class, 'reviseForm'])->name('ppic.revise-form');
            Route::get('ppic/revisi-all', [PPICController::class, 'showAllRevisions'])->name('ppic.revisi-show');

            //maintanance
            Route::get('maintanance/count-sop', [MaintananceController::class, 'countSop'])->name('maintanance.count-sop');
            Route::get('maintanance/count-wi', [MaintananceController::class, 'countWi'])->name('maintanance.count-wi');
            Route::get('maintanance/count-form', [MaintananceController::class, 'countForm'])->name('maintanance.count-form');
            Route::get('maintanance/count-all', [MaintananceController::class, 'countAll'])->name('maintanance.count-all');
            Route::get('maintanance', [MaintananceController::class, 'index'])->name('maintanance.index');
            Route::get('maintanance/sop-list-partial', [MaintananceController::class, 'contentListPartial'])->name('maintanance.content-list-partial');

            Route::post('maintanance/store-sop', [MaintananceController::class, 'storeSop'])->name('maintanance.store-sop');
            Route::get('maintanance/{sop}/edit-sop', [MaintananceController::class, 'editSop'])->name('maintanance.edit-sop');
            Route::put('maintanance/{sop}/update-sop', [MaintananceController::class, 'updateSop'])->name('maintanance.update-sop');
            Route::delete('maintanance/{sop}/destroy-sop', [MaintananceController::class, 'destroySop'])->name('maintanance.destroy-sop');
            Route::get('maintanance/{sop}/revisi-sop-data', [MaintananceController::class, 'revisiSOP'])->name('maintanance.revisi-sop');
            Route::post('maintanance/{sop}/revisi-sop', [MaintananceController::class, 'reviseSOP'])->name('maintanance.revise-sop');

            Route::post('maintanance/{sop}/store-wi', [MaintananceController::class, 'storeWI'])->name('maintanance.store-wi');
            Route::get('maintanance/{wi}/edit-wi', [MaintananceController::class, 'editWI'])->name('maintanance.edit-wi');
            Route::put('maintanance/{wi}/update-wi', [MaintananceController::class, 'updateWI'])->name('maintanance.update-wi');
            Route::delete('maintanance/{wi}/destroy-wi', [MaintananceController::class, 'destroyWI'])->name('maintanance.destroy-wi');
            Route::get('maintanance/{wi}/revisi-wi-data', [MaintananceController::class, 'revisiWI'])->name('maintanance.revisi-wi');
            Route::post('maintanance/{wi}/revisi-wi', [MaintananceController::class, 'reviseWI'])->name('maintanance.revise-wi');

            Route::post('maintanance/{wi}/store-form', [MaintananceController::class, 'storeForm'])->name('maintanance.store-form');
            Route::get('maintanance/{form}/edit-form', [MaintananceController::class, 'editForm'])->name('maintanance.edit-form');
            Route::put('maintanance/{form}/update-form', [MaintananceController::class, 'updateForm'])->name('maintanance.update-form');
            Route::delete('maintanance/{form}/destroy-form', [MaintananceController::class, 'destroyForm'])->name('maintanance.destroy-form');
            Route::get('maintanance/{form}/revisi-form-data', [MaintananceController::class, 'revisiForm'])->name('maintanance.revisi-form');
            Route::post('maintanance/{form}/revisi-form', [MaintananceController::class, 'reviseForm'])->name('maintanance.revise-form');
            Route::get('maintanance/revisi-all', [MaintananceController::class, 'showAllRevisions'])->name('maintanance.revisi-show');

            // human capital
            Route::get('human-capital/count-sop', [HumanCapitalController::class, 'countSop'])->name('human-capital.count-sop');
            Route::get('human-capital/count-wi', [HumanCapitalController::class, 'countWi'])->name('human-capital.count-wi');
            Route::get('human-capital/count-form', [HumanCapitalController::class, 'countForm'])->name('human-capital.count-form');
            Route::get('human-capital/count-all', [HumanCapitalController::class, 'countAll'])->name('human-capital.count-all');
            Route::get('human-capital', [HumanCapitalController::class, 'index'])->name('human-capital.index');
            Route::get('human-capital/sop-list-partial', [HumanCapitalController::class, 'contentListPartial'])->name('human-capital.content-list-partial');

            Route::post('human-capital/store-sop', [HumanCapitalController::class, 'storeSop'])->name('human-capital.store-sop');
            Route::get('human-capital/{sop}/edit-sop', [HumanCapitalController::class, 'editSop'])->name('human-capital.edit-sop');
            Route::put('human-capital/{sop}/update-sop', [HumanCapitalController::class, 'updateSop'])->name('human-capital.update-sop');
            Route::delete('human-capital/{sop}/destroy-sop', [HumanCapitalController::class, 'destroySop'])->name('human-capital.destroy-sop');
            Route::get('human-capital/{sop}/revisi-sop-data', [HumanCapitalController::class, 'revisiSOP'])->name('human-capital.revisi-sop');
            Route::post('human-capital/{sop}/revisi-sop', [HumanCapitalController::class, 'reviseSOP'])->name('human-capital.revise-sop');

            Route::post('human-capital/{sop}/store-wi', [HumanCapitalController::class, 'storeWI'])->name('human-capital.store-wi');
            Route::get('human-capital/{wi}/edit-wi', [HumanCapitalController::class, 'editWI'])->name('human-capital.edit-wi');
            Route::put('human-capital/{wi}/update-wi', [HumanCapitalController::class, 'updateWI'])->name('human-capital.update-wi');
            Route::delete('human-capital/{wi}/destroy-wi', [HumanCapitalController::class, 'destroyWI'])->name('human-capital.destroy-wi');
            Route::get('human-capital/{wi}/revisi-wi-data', [HumanCapitalController::class, 'revisiWI'])->name('human-capital.revisi-wi');
            Route::post('human-capital/{wi}/revisi-wi', [HumanCapitalController::class, 'reviseWI'])->name('human-capital.revise-wi');

            Route::post('human-capital/{wi}/store-form', [HumanCapitalController::class, 'storeForm'])->name('human-capital.store-form');
            Route::get('human-capital/{form}/edit-form', [HumanCapitalController::class, 'editForm'])->name('human-capital.edit-form');
            Route::put('human-capital/{form}/update-form', [HumanCapitalController::class, 'updateForm'])->name('human-capital.update-form');
            Route::delete('human-capital/{form}/destroy-form', [HumanCapitalController::class, 'destroyForm'])->name('human-capital.destroy-form');
            Route::get('human-capital/{form}/revisi-form-data', [HumanCapitalController::class, 'revisiForm'])->name('human-capital.revisi-form');
            Route::post('human-capital/{form}/revisi-form', [HumanCapitalController::class, 'reviseForm'])->name('human-capital.revise-form');
            Route::get('human-capital/revisi-all', [HumanCapitalController::class, 'showAllRevisions'])->name('human-capital.revisi-show');

            // engineering
            Route::get('engineering/count-sop', [EngineeringController::class, 'countSop'])->name('engineering.count-sop');
            Route::get('engineering/count-wi', [EngineeringController::class, 'countWi'])->name('engineering.count-wi');
            Route::get('engineering/count-form', [EngineeringController::class, 'countForm'])->name('engineering.count-form');
            Route::get('engineering/count-all', [EngineeringController::class, 'countAll'])->name('engineering.count-all');
            Route::get('engineering', [EngineeringController::class, 'index'])->name('engineering.index');
            Route::get('engineering/sop-list-partial', [EngineeringController::class, 'contentListPartial'])->name('engineering.content-list-partial');

            Route::post('engineering/store-sop', [EngineeringController::class, 'storeSop'])->name('engineering.store-sop');
            Route::get('engineering/{sop}/edit-sop', [EngineeringController::class, 'editSop'])->name('engineering.edit-sop');
            Route::put('engineering/{sop}/update-sop', [EngineeringController::class, 'updateSop'])->name('engineering.update-sop');
            Route::delete('engineering/{sop}/destroy-sop', [EngineeringController::class, 'destroySop'])->name('engineering.destroy-sop');
            Route::get('engineering/{sop}/revisi-sop-data', [EngineeringController::class, 'revisiSOP'])->name('engineering.revisi-sop');
            Route::post('engineering/{sop}/revisi-sop', [EngineeringController::class, 'reviseSOP'])->name('engineering.revise-sop');

            Route::post('engineering/{sop}/store-wi', [EngineeringController::class, 'storeWI'])->name('engineering.store-wi');
            Route::get('engineering/{wi}/edit-wi', [EngineeringController::class, 'editWI'])->name('engineering.edit-wi');
            Route::put('engineering/{wi}/update-wi', [EngineeringController::class, 'updateWI'])->name('engineering.update-wi');
            Route::delete('engineering/{wi}/destroy-wi', [EngineeringController::class, 'destroyWI'])->name('engineering.destroy-wi');
            Route::get('engineering/{wi}/revisi-wi-data', [EngineeringController::class, 'revisiWI'])->name('engineering.revisi-wi');
            Route::post('engineering/{wi}/revisi-wi', [EngineeringController::class, 'reviseWI'])->name('engineering.revise-wi');

            Route::post('engineering/{wi}/store-form', [EngineeringController::class, 'storeForm'])->name('engineering.store-form');
            Route::get('engineering/{form}/edit-form', [EngineeringController::class, 'editForm'])->name('engineering.edit-form');
            Route::put('engineering/{form}/update-form', [EngineeringController::class, 'updateForm'])->name('engineering.update-form');
            Route::delete('engineering/{form}/destroy-form', [EngineeringController::class, 'destroyForm'])->name('engineering.destroy-form');
            Route::get('engineering/{form}/revisi-form-data', [EngineeringController::class, 'revisiForm'])->name('engineering.revisi-form');
            Route::post('engineering/{form}/revisi-form', [EngineeringController::class, 'reviseForm'])->name('engineering.revise-form');
            Route::get('engineering/revisi-all', [EngineeringController::class, 'showAllRevisions'])->name('engineering.revisi-show');

            // irga
            Route::get('irga/count-sop', [IRGAController::class, 'countSop'])->name('irga.count-sop');
            Route::get('irga/count-wi', [IRGAController::class, 'countWi'])->name('irga.count-wi');
            Route::get('irga/count-form', [IRGAController::class, 'countForm'])->name('irga.count-form');
            Route::get('irga/count-all', [IRGAController::class, 'countAll'])->name('irga.count-all');
            Route::get('irga', [IRGAController::class, 'index'])->name('irga.index');
            Route::get('irga/sop-list-partial', [IRGAController::class, 'contentListPartial'])->name('irga.content-list-partial');

            Route::post('irga/store-sop', [IRGAController::class, 'storeSop'])->name('irga.store-sop');
            Route::get('irga/{sop}/edit-sop', [IRGAController::class, 'editSop'])->name('irga.edit-sop');
            Route::put('irga/{sop}/update-sop', [IRGAController::class, 'updateSop'])->name('irga.update-sop');
            Route::delete('irga/{sop}/destroy-sop', [IRGAController::class, 'destroySop'])->name('irga.destroy-sop');
            Route::get('irga/{sop}/revisi-sop-data', [IRGAController::class, 'revisiSOP'])->name('irga.revisi-sop');
            Route::post('irga/{sop}/revisi-sop', [IRGAController::class, 'reviseSOP'])->name('irga.revise-sop');

            Route::post('irga/{sop}/store-wi', [IRGAController::class, 'storeWI'])->name('irga.store-wi');
            Route::get('irga/{wi}/edit-wi', [IRGAController::class, 'editWI'])->name('irga.edit-wi');
            Route::put('irga/{wi}/update-wi', [IRGAController::class, 'updateWI'])->name('irga.update-wi');
            Route::delete('irga/{wi}/destroy-wi', [IRGAController::class, 'destroyWI'])->name('irga.destroy-wi');
            Route::get('irga/{wi}/revisi-wi-data', [IRGAController::class, 'revisiWI'])->name('irga.revisi-wi');
            Route::post('irga/{wi}/revisi-wi', [IRGAController::class, 'reviseWI'])->name('irga.revise-wi');

            Route::post('irga/{wi}/store-form', [IRGAController::class, 'storeForm'])->name('irga.store-form');
            Route::get('irga/{form}/edit-form', [IRGAController::class, 'editForm'])->name('irga.edit-form');
            Route::put('irga/{form}/update-form', [IRGAController::class, 'updateForm'])->name('irga.update-form');
            Route::delete('irga/{form}/destroy-form', [IRGAController::class, 'destroyForm'])->name('irga.destroy-form');
            Route::get('irga/{form}/revisi-form-data', [IRGAController::class, 'revisiForm'])->name('irga.revisi-form');
            Route::post('irga/{form}/revisi-form', [IRGAController::class, 'reviseForm'])->name('irga.revise-form');
            Route::get('irga/revisi-all', [IRGAController::class, 'showAllRevisions'])->name('irga.revisi-show');

            // she
            Route::get('she/count-sop', [SHEController::class, 'countSop'])->name('she.count-sop');
            Route::get('she/count-wi', [SHEController::class, 'countWi'])->name('she.count-wi');
            Route::get('she/count-form', [SHEController::class, 'countForm'])->name('she.count-form');
            Route::get('she/count-all', [SHEController::class, 'countAll'])->name('she.count-all');
            Route::get('she', [SHEController::class, 'index'])->name('she.index');
            Route::get('she/sop-list-partial', [SHEController::class, 'contentListPartial'])->name('she.content-list-partial');

            Route::post('she/store-sop', [SHEController::class, 'storeSop'])->name('she.store-sop');
            Route::get('she/{sop}/edit-sop', [SHEController::class, 'editSop'])->name('she.edit-sop');
            Route::put('she/{sop}/update-sop', [SHEController::class, 'updateSop'])->name('she.update-sop');
            Route::delete('she/{sop}/destroy-sop', [SHEController::class, 'destroySop'])->name('she.destroy-sop');
            Route::get('she/{sop}/revisi-sop-data', [SHEController::class, 'revisiSOP'])->name('she.revisi-sop');
            Route::post('she/{sop}/revisi-sop', [SHEController::class, 'reviseSOP'])->name('she.revise-sop');

            Route::post('she/{sop}/store-wi', [SHEController::class, 'storeWI'])->name('she.store-wi');
            Route::get('she/{wi}/edit-wi', [SHEController::class, 'editWI'])->name('she.edit-wi');
            Route::put('she/{wi}/update-wi', [SHEController::class, 'updateWI'])->name('she.update-wi');
            Route::delete('she/{wi}/destroy-wi', [SHEController::class, 'destroyWI'])->name('she.destroy-wi');
            Route::get('she/{wi}/revisi-wi-data', [SHEController::class, 'revisiWI'])->name('she.revisi-wi');
            Route::post('she/{wi}/revisi-wi', [SHEController::class, 'reviseWI'])->name('she.revise-wi');

            Route::post('she/{wi}/store-form', [SHEController::class, 'storeForm'])->name('she.store-form');
            Route::get('she/{form}/edit-form', [SHEController::class, 'editForm'])->name('she.edit-form');
            Route::put('she/{form}/update-form', [SHEController::class, 'updateForm'])->name('she.update-form');
            Route::delete('she/{form}/destroy-form', [SHEController::class, 'destroyForm'])->name('she.destroy-form');
            Route::get('she/{form}/revisi-form-data', [SHEController::class, 'revisiForm'])->name('she.revisi-form');
            Route::post('she/{form}/revisi-form', [SHEController::class, 'reviseForm'])->name('she.revise-form');
            Route::get('she/revisi-all', [SHEController::class, 'showAllRevisions'])->name('she.revisi-show');
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
