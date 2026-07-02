<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AbonnementProController;
use App\Http\Controllers\ForfaitController;
use App\Http\Controllers\PromotionController;
// use App\Http\Controllers\LocationController;

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

Route::middleware(['auth', 'check.etablissement'])->group(function () {
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::post('/etablissement/masquer-annonce', [DashboardController::class, 'hideAnnonce'])->name('etablissement.hideAnnonce');

    Route::post('/logout', [DashboardController::class, 'logout'])->name('logout');

    Route::get('/profil', [DashboardController::class, 'profil'])->name('profil.index');
    Route::post('/profil/update', [DashboardController::class, 'updateProfil'])->name('profil.update');
    Route::post('/password/update', [DashboardController::class, 'updatepassword'])->name('password.update');

    // Routes pour l'enregistrement de l'établissement
    Route::get('/etablissement/create', [EtablissementController::class, 'create'])->name('etablissement.create');
    Route::post('/etablissement/store', [EtablissementController::class, 'storeEtablissement'])->name('etablissement.store');
    Route::get('/etablissement/edit', [EtablissementController::class, 'edit'])->name('etablissement.edit');
    Route::post('/etablissement/update/{id}', [EtablissementController::class, 'update'])->name('etablissement.update');

    // Route::get('/get-villes/{paysId}', [LocationController::class, 'getVilles']);
    // Route::get('/get-communes/{villeId}', [LocationController::class, 'getCommunes']);

    Route::get('/liste-des-services', [ServiceController::class, 'index'])->name('service.index');
    Route::post('/store-services', [ServiceController::class, 'store'])->name('service.store');
    Route::post('/update-services/{id}', [ServiceController::class, 'update'])->name('service.update');
    Route::delete('services/{id}', [ServiceController::class, 'destroy'])->name('service.destroy');

    Route::get('/liste-des-articles', [ArticleController::class, 'index'])->name('article.index');
    Route::post('/store-articles', [ArticleController::class, 'store'])->name('article.store');
    Route::post('/update-articles/{id}', [ArticleController::class, 'update'])->name('article.update');
    Route::delete('articles/{id}', [ArticleController::class, 'destroy'])->name('article.destroy');

    Route::get('/historique-des-abonnements', [AbonnementProController::class, 'index'])->name('abonnement.index');
    Route::get('/recu-abonnement/{id}', [AbonnementProController::class, 'imprimerRecu'])->name('imprimerRecu');


    Route::get('/les-forfaits', [ForfaitController::class, 'index'])->name('forfait.index');

    Route::get('/liste-des-promotions', [PromotionController::class, 'index'])->name('promotion.index');
    Route::post('/store-promotions', [PromotionController::class, 'store'])->name('promotion.store');
    Route::post('/update-promotions/{id}', [PromotionController::class, 'update'])->name('promotion.update');
    Route::delete('promotions/{id}', [PromotionController::class, 'destroy'])->name('promotion.destroy');

});


Route::get('/', [AuthController::class, 'showlogin'])->name('login');

Route::get('/login', [AuthController::class, 'showlogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('logins');

Route::get('/register', [AuthController::class, 'showregister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('registers');

// Mot de passe oublié - Affichage du formulaire
Route::get('/password/forget', [AuthController::class, 'showpasswordforget'])->name('password.forget');

// Mot de passe oublié - Envoi OTP
Route::post('/password/forget', [AuthController::class, 'postPasswordForget'])->name('post-password.forget');

// Saisie OTP
Route::get('/otp', function() { return view('auth.otp'); })->name('auth.otp');
Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('auth.otp.verify');

// Réinitialisation du mot de passe
Route::get('/password/reset', function() { return view('auth.password_reset'); })->name('password.reset.form');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset.submit');

// Routes pour les abonnements professionnels
Route::middleware(['auth'])->group(function () {
    Route::get('/abonnements', [AbonnementProController::class, 'index'])->name('abonnement.index');
    Route::post('/abonnements', [AbonnementProController::class, 'store'])->name('abonnement.store');
    Route::get('/abonnements/{id}/edit', [AbonnementProController::class, 'edit'])->name('abonnement.edit');
    Route::put('/abonnements/{id}', [AbonnementProController::class, 'update'])->name('abonnement.update');
    Route::delete('/abonnements/{id}', [AbonnementProController::class, 'destroy'])->name('abonnement.destroy');
});
