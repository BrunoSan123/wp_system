<?php

use App\Http\Controllers\ConfigDeleteController;
use App\Http\Controllers\CsvReaderController;
use App\Http\Controllers\EditorController;
use App\Http\Controllers\DasboardController;
use App\Http\Controllers\DriveCredentialController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\GooGleDocsController;
use App\Http\Controllers\GptController;
use App\Http\Controllers\iaCredentialController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\PostContentController;
use App\Http\Controllers\WpController;
use App\Http\Controllers\WpCredentialController;
use App\Services\PostFileService;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/register', [DasboardController::class,'register'])->name('dashboard.register');
Route::get('/upload_csv',[DasboardController::class,'importCsv'])->name('dashboard.uploadCsv');
Route::get('/gdriveport',[DasboardController::class,'gDrivePort'])->name('dashboard.gDriveConfig');
Route::get('/', [DasboardController::class, 'login'])->name('login');
Route::get('/index', [DasboardController::class, 'index'])->name('dashboard.index');
Route::get('/dashboard', [DasboardController::class, 'show'])->name('dashboard.show');
Route::get('/profile', [DasboardController::class, 'profile'])->name('dashboard.profile');
Route::get('/edior_created',[DasboardController::class,'ediorCreated'])->name('dashboard.editorCreated');
Route::get('/docs_created',[DasboardController::class,'docCreated'])->name('dashboard.DocumentCreated');
Route::get('/content_creation',[DasboardController::class,'contentCreation'])->name('dashboard.contentConfig');
Route::get('/content_imported',[DasboardController::class,'docImported'])->name('dashboard.DocumentImported');
Route::get('/config_created',[DasboardController::class,'configCreated'])->name('configCreated');
Route::get('/token_inserted',[DasboardController::class,'tokenInserted'])->name('tokenInserted');
Route::get('/post_creation',[DasboardController::class,'postCreation'])->name('dashboard.createPost');
Route::get('/upload_doc',[DasboardController::class,'DocsUpload'])->name('doc');
Route::get('/create-doc',[DasboardController::class,'DocCreation'])->name('createDoc');
Route::get('/auth/google', [GoogleAuthController::class,'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class,'handleGoogleCallback'])->name('google.callback');
Route::get('/list_content',[DasboardController::class,'listPostConfig'])->name('dashboard.SumitPosts');
Route::get('/insert_gpt_token',[DasboardController::class,'insertGptToken'])->name('dashboard.configia');
Route::get('/submit_wp',[DasboardController::class,'insertWpCredential'])->name('dashboard.wp');
Route::get('/site_credential_created',[DasboardController::class,'siteCredentialCreated'])->name('credentialCreated');
Route::get('/list_credential',[DasboardController::class,'listWpCredential'])->name('listCredential');
Route::get('/list_ia_credential',[DasboardController::class,'listIaCredentials'])->name('listIaCredentials');
Route::get('/deleted_token',[DasboardController::class,'tokenDeleted'])->name('deletedToken');
Route::get('/list_editor',[DasboardController::class,'ListEditor'])->name('listEditor');
Route::get('/config_updated',[DasboardController::class,'configUpdated'])->name('configUpdated');
Route::get('/teste',[DasboardController::class,'gptTeste']);
Route::get('/yoastforce',[DasboardController::class,'yoastforce'])->name('yoastforce');
Route::get('/register_site_table',[DasboardController::class,'registerSiteTeste'])->name('importSite');
Route::get('/changelog',[DasboardController::class,'changelog'])->name('changelog');



Route::post('/createEditor', [EditorController::class,'processEditor'])->name('processEditor');
Route::post('/submit_file',[CsvReaderController::class,'ImportCsv']);
Route::post('/insert_post_content',[PostContentController::class,'saveContent']);
Route::post('/gpt_query',[GptController::class,'generatePost']);
Route::post('/process_doc',[GooGleDocsController::class,'insertDocOnDB']);
Route::post('/create_doc',[GooGleDocsController::class,'createDocFromDb']);
Route::post('/post_content',[WpController::class,'createBlogPost']);
Route::post('/update_yoaust',[WpController::class,'updateYoast']);
Route::post('/validate',[loginController::class,'validateLogin'])->name('validateLogin');
Route::get('/quit',[loginController::class,'logoff'])->name('logoff');
Route::post('/submit_ia_token',[iaCredentialController::class,'insertCredential'])->name('insertIaToken');
Route::post('/submit_wp_credential',[WpCredentialController::class,'saveWpCredential'])->name('saveWpCredential');
Route::post('/create_google_credential',[DriveCredentialController::class,'insertGoogleCredentials']);
Route::post('/update_google_credential',[DriveCredentialController::class,'updateGoogleCredentials']);
Route::delete('/remove_config',[ConfigDeleteController::class,'deleteConfig']);
Route::delete('/editor/{id}', [EditorController::class,'destroy'])->name('editor.destroy');
Route::delete('/wp_crential/{id}',[WpCredentialController::class,'deleteWpCredential'])->name('credentialDelete');
Route::delete('/delete_token/{id}',[iaCredentialController::class,'deleteCredential'])->name('deleteToken');
Route::put('/update_user',[EditorController::class,'updateEditor']);
Route::put('/update_credentials',[WpCredentialController::class,'updateWpCredential']);
Route::put('/update_config',[PostContentController::class,'updateConfig']);


