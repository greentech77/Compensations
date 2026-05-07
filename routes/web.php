<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthenticationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

use App\Http\Controllers\User\UserController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Compenzation\CompenzationController;
use App\Http\Controllers\Bill\BillController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ContractsExportController;

use Inertia\Inertia;

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
/*Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});*/

/*Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');*/

//Route::get('/dashboard', [UserController::class, 'getDashboard'])->middleware(['auth', 'verified'])->name('dashboard');
//Route::get('/entities', [UserController::class, 'getEntities'])->middleware(['auth', 'verified'])->name('entities');


Route::middleware(['auth:web'])->group(function () {

Route::get('/', [UserController::class, 'getDashboard'])->name('home');
//Route::get('/dashboard', [UserController::class, 'getEntities'])->name('dashboard');
Route::get('/dashboard', [UserController::class, 'getDashboard'])->name('dashboard');
Route::get('/entities', [UserController::class, 'getEntities'])->name('entities');
Route::get('/entities/{id}', [UserController::class, 'getEntity'])->name('entities.entity');
Route::patch('/entities/{id}', [UserController::class, 'patchEntity'])->name('entities.entity.patch');
Route::get('/entities/entity/new', [UserController::class, 'registerEntity'])->name('entities.entity.register');
Route::get('/entities/{entityId}/compenzation/{compenzationId}/pdf/{type}', [UserController::class, 'downloadCompenzationPdf'])->name('entities.compenzation.pdf.download');

Route::get('/compenzations', [CompenzationController::class, 'getCompenzations'])->name('compenzations');
Route::get('/compenzations/stats', [CompenzationController::class, 'stats'])->name('compenzations.stats');
Route::get('/compenzations/stats/export', [CompenzationController::class, 'exportStats'])->name('compenzations.stats.export');
Route::get('/compenzations/export', [CompenzationController::class, 'exportCompenzations'])->name('compenzations.export');
Route::get('/compenzations/{id}', [CompenzationController::class, 'getCompenzation'])->name('compenzations.compenzation');
Route::patch('/compenzations/{id}', [CompenzationController::class, 'patchCompenzation'])->name('compenzations.compenzation.patch');
Route::get('/compenzations/{id}/pdf/{type}', [CompenzationController::class, 'downloadCompenzationPdf'])->name('compenzations.compenzation.pdf.download');
Route::post('/compenzations/{id}/pdf/regenerate', [CompenzationController::class, 'regeneratePdfs'])->name('compenzations.compenzation.pdf.regenerate');
Route::get('/compenzations/compenzation/new', [CompenzationController::class, 'addCompenzation'])->name('compenzations.compenzation.new');

Route::get('/bills', [BillController::class, 'getBills'])->name('bills');
Route::post('/bills/specification', [BillController::class, 'createSpecification'])->name('bills.specification');
Route::get('/bills/{id}/pdf', [BillController::class, 'downloadBillPdf'])->name('bills.pdf.download');

/**
 * Post za enterprise data step registracijo
 */
Route::post('/register/data', [RegisterController::class, 'postEntityData'])->name('register.data');


/**
 * Final post za enterprise registracijo
 */

Route::post('/register/entity', [RegisterController::class, 'postEntity'])->name('register.entity');

/**
 * Post za compenzation data step 
 */
Route::post('/compenzation/data', [CompenzationController::class, 'postCompenzationData'])->name('compenzation.data');


/**
 * Final post za compenzation vnos
 */

Route::post('/compenzation/add', [CompenzationController::class, 'postCompenzation'])->name('compenzation.add');

// Export routes
Route::get('/exports', [ExportController::class, 'index'])->name('exports.index');
Route::get('/exports/bills', [ExportController::class, 'bills'])->name('exports.bills');
Route::match(['get', 'post'], '/exports/bills/download', [ExportController::class, 'exportBills'])->name('exports.bills.export');
Route::get('/exports/bills/files/{filename}', [ExportController::class, 'downloadBillsFile'])
    ->where('filename', '[A-Za-z0-9._\-]+')
    ->name('exports.bills.file');
Route::get('/exports/contracts', [ContractsExportController::class, 'contracts'])->name('exports.contracts');
Route::match(['get', 'post'], '/exports/contracts/download', [ContractsExportController::class, 'exportContracts'])->name('exports.contracts.export');
Route::get('/exports/contracts/files/{filename}', [ContractsExportController::class, 'downloadContractsFile'])
    ->where('filename', '[A-Za-z0-9._\-]+')
    ->name('exports.contracts.file');
Route::get('/exports/compenzations', [ExportController::class, 'compenzations'])->name('exports.compenzations');
Route::get('/exports/compenzations/files/{filename}', [ExportController::class, 'downloadCompenzationsFile'])
    ->where('filename', '[A-Za-z0-9._\-]+')
    ->name('exports.compenzations.file');

});

/*Route::middleware(['auth:admin'])->group(function () {
	Route::get('/admin', function () {
	        return redirect()->route('admin.dashboard');
	    })->name('admin.home');

Route::get('/admin/dashboard', [AdminController::class, 'getDashboard'])->name('admin.dashboard');

});*/

Route::get('/locale/{locale?}',[LocaleController::class, 'getLocale'])->name('locale.get');
Route::post('/locale/{locale?}',[LocaleController::class, 'postLocale'])->name('locale.post');

/**
 * Authentication routes
 */

//Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

/*Route::get('/admin/login', [AuthenticationController::class, 'getLogin'])->name('admin.login');
Route::post('/admin/login', [AuthenticationController::class, 'postLogin'])->name('admin.login.post');
Route::post('/admin/logout', [AuthenticationController::class, 'postLogout'])->name('admin.logout.post');
*/

require __DIR__.'/auth.php';
