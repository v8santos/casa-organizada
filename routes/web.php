<?php

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\HouseholdController;
use App\Http\Controllers\Web\PurchaseController;
use App\Http\Controllers\Web\ShoppingListController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'require-household'])->group(function () {
    Route::get('', fn () => redirect()->route('dashboard'))->name('home');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::controller(HouseholdController::class)->withoutMiddleware('require-household')->group(function () {
        Route::prefix('households')->name('households.')->group(function () {
            Route::get('', [HouseholdController::class, 'index'])->name('index');
            Route::get('create', fn () => view('household.create'))->name('create');
            Route::post('store', 'store')->name('store');
            Route::post('set', 'setHousehold')->name('set');
        });
    });

    Route::controller(ShoppingListController::class)->group(function () {
        Route::prefix('shopping-lists')->name('shopping-lists.')->group(function () {
            Route::get('', 'indexPage')->name('index');
            Route::get('create', 'createPage')->name('create');
            Route::get('edit/{listId}', 'editPage')->name('edit');
            Route::get('{listId}/share/whatsapp', 'shareWhatsApp')->name('share.whatsapp');

            Route::get('find/{listId}', 'find')->name('find');
            Route::post('store', 'store')->name('store');
            Route::post('{listId}/items', 'storeItem')->name('items.store');
            Route::patch('{listId}/items/{itemId}', 'updateItem')->name('items.update');
            Route::delete('{listId}/items/{itemId}', 'destroyItem')->name('items.destroy');
            Route::put('update', 'update')->name('update');
        });
    });

    Route::controller(PurchaseController::class)->group(function () {
        Route::prefix('purchases')->name('purchases.')->group(function () {
            Route::get('', 'indexPage')->name('index');
            Route::get('edit/{purchaseId}', 'editPage')->name('edit');

            Route::post('store', 'store')->name('store');
            Route::post('{purchase}/items/store', 'storeItem')->name('items.store');
            Route::put('{purchase}/shopping-list/{shoppingList}/items', 'syncItems')->name('shopping-list.items');
            Route::delete('{purchase}/items/{itemId}', 'destroyItem')->name('items.destroy');
        });
    });

    Route::controller(UserController::class)->group(function () {
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('create', 'createPage')->name('create');

            Route::post('store', 'store')->name('store');
        });
    });
});

require __DIR__.'/settings.php';
