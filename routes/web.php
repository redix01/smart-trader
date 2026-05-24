<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\DepositController as AdminDepositController;
use App\Http\Controllers\Admin\DepositMethodController as AdminDepositMethodController;
use App\Http\Controllers\Admin\ExpertController as AdminExpertController;
use App\Http\Controllers\Admin\KycController as AdminKycController;
use App\Http\Controllers\Admin\MiningPlanController as AdminMiningPlanController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PropertyProjectController as AdminPropertyProjectController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\StakingPlanController as AdminStakingPlanController;
use App\Http\Controllers\Admin\SwapController as AdminSwapController;
use App\Http\Controllers\Admin\TradeRoomController as AdminTradeRoomController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\WithdrawalController as AdminWithdrawalController;
use App\Http\Controllers\AssetsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\ExpertsController;
use App\Http\Controllers\MarketsController;
use App\Http\Controllers\MiningController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RealEstateController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\StakingController;
use App\Http\Controllers\SwapController;
use App\Http\Controllers\TradesController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::view('/', 'frontpage.index')->name('landing');
Route::view('/about', 'frontpage.about')->name('landing.about');
Route::view('/faqs', 'frontpage.faqs')->name('landing.faqs');
Route::view('/copy', 'frontpage.copy')->name('landing.copy');
Route::view('/crypto', 'frontpage.crypto')->name('landing.crypto');
Route::view('/forex', 'frontpage.forex')->name('landing.forex');
Route::view('/stocks', 'frontpage.stocks')->name('landing.stocks');
Route::view('/privacy-policy', 'frontpage.privacy-policy')->name('landing.privacy');
Route::view('/rules', 'frontpage.rules')->name('landing.rules');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/kyc', [KycController::class, 'index'])->name('kyc');
    Route::post('/kyc', [KycController::class, 'store'])->name('kyc.store');
    Route::get('/assets', AssetsController::class)->name('assets');
    Route::get('/markets', MarketsController::class)->name('markets');
    Route::post('/markets/{marketPairId}/favorite', [MarketsController::class, 'toggleFavorite'])->name('markets.favorite');
    Route::get('/trades', [TradesController::class, 'index'])->name('trades');
    Route::post('/trades', [TradesController::class, 'store'])->name('trades.store');
    Route::get('/swap', [SwapController::class, 'index'])->name('swap');
    Route::post('/swap/quote', [SwapController::class, 'quote'])->name('swap.quote');
    Route::post('/swap', [SwapController::class, 'store'])->name('swap.store');
    Route::get('/deposit', [DepositController::class, 'index'])->name('deposit');
    Route::post('/deposit', [DepositController::class, 'store'])->name('deposit.store');
    Route::get('/stakes', [StakingController::class, 'index'])->name('stakes');
    Route::post('/stakes', [StakingController::class, 'store'])->name('stakes.store');
    Route::get('/withdraw', [WithdrawalController::class, 'index'])->name('withdraw');
    Route::post('/withdraw', [WithdrawalController::class, 'store'])->name('withdraw.store');
    Route::get('/mining', [MiningController::class, 'index'])->name('mining');
    Route::post('/mining', [MiningController::class, 'store'])->name('mining.store');
    Route::get('/experts', [ExpertsController::class, 'index'])->name('experts');
    Route::post('/experts', [ExpertsController::class, 'store'])->name('experts.store');
    Route::get('/realestate', [RealEstateController::class, 'index'])->name('realestate');
    Route::post('/realestate', [RealEstateController::class, 'store'])->name('realestate.store');
    Route::get('/support', SupportController::class)->name('support');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/trade-room', [AdminTradeRoomController::class, 'index'])->name('trade-room.index');
    Route::post('/trade-room', [AdminTradeRoomController::class, 'store'])->name('trade-room.store');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');
        Route::patch('/{user}', [AdminUserController::class, 'update'])->name('update');
        Route::post('/{user}/wallets/adjust', [AdminUserController::class, 'adjustWallet'])->name('wallets.adjust');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('kyc')->name('kyc.')->group(function () {
        Route::get('/', [AdminKycController::class, 'index'])->name('index');
        Route::get('/{kyc}', [AdminKycController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [AdminKycController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [AdminKycController::class, 'reject'])->name('reject');
    });

    Route::prefix('deposits')->name('deposits.')->group(function () {
        Route::get('/', [AdminDepositController::class, 'index'])->name('index');
        Route::get('/{deposit}', [AdminDepositController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [AdminDepositController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [AdminDepositController::class, 'reject'])->name('reject');
    });

    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [AdminWithdrawalController::class, 'index'])->name('index');
        Route::get('/{withdrawal}', [AdminWithdrawalController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [AdminWithdrawalController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [AdminWithdrawalController::class, 'reject'])->name('reject');
    });

    Route::resource('staking-plans', AdminStakingPlanController::class)->except(['show', 'create', 'edit']);
    Route::resource('mining-plans', AdminMiningPlanController::class)->except(['show', 'create', 'edit']);
    Route::resource('experts', AdminExpertController::class)->except(['show', 'create', 'edit']);
    Route::resource('property-projects', AdminPropertyProjectController::class)->except(['show', 'create', 'edit']);

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
        Route::patch('/{order}', [AdminOrderController::class, 'update'])->name('update');
    });

    Route::prefix('swaps')->name('swaps.')->group(function () {
        Route::get('/', [AdminSwapController::class, 'index'])->name('index');
        Route::get('/{swap}', [AdminSwapController::class, 'show'])->name('show');
    });

    Route::resource('deposit-methods', AdminDepositMethodController::class)->except(['show', 'create', 'edit']);

    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
