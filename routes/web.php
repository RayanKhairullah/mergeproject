<?php

use Illuminate\Support\Facades\Route;

Route::get('/', \App\Livewire\Home::class)->name('home');

Route::get('/dashboard', \App\Livewire\Dashboard::class)->middleware(['auth', 'verified'])->name('dashboard');

// Public Vehicle Management Routes
Route::prefix('vehicles')->as('vehicles.')->group(function (): void {
    Route::get('/monitor', \App\Livewire\Frontend\VehicleMonitor::class)->name('monitor');
    Route::get('/loan/{vehicle?}', \App\Livewire\Frontend\LoanForm::class)->name('loan');
    Route::get('/return', \App\Livewire\Frontend\PublicReturnForm::class)->name('return');
    Route::get('/expense', \App\Livewire\Frontend\ExpenseForm::class)->name('expense');
});

// Public Meeting Routes
Route::prefix('meetings')->as('meetings.')->group(function (): void {
    Route::get('/monitor', \App\Livewire\Frontend\MeetingMonitor::class)->name('monitor');
});

// Digital Library Routes
Route::prefix('books')->as('books.')->group(function (): void {
    Route::get('/', \App\Livewire\Frontend\Books\Index::class)->name('index');
    Route::get('/{book:slug}', \App\Livewire\Frontend\Books\Show::class)->name('show');
    Route::get('/{book:slug}/download', [\App\Http\Controllers\BookController::class, 'download'])->name('download')->middleware('auth');
});

// API Routes for Digital Library
Route::prefix('api')->as('api.')->group(function (): void {
    Route::get('/books/search', [\App\Http\Controllers\BookController::class, 'search'])->name('books.search');
});

Route::middleware(['auth'])->group(function (): void {

    // Impersonations
    Route::post('/impersonate/{user}', [\App\Http\Controllers\ImpersonationController::class, 'store'])->name('impersonate.store')->middleware('can:impersonate');
    Route::delete('/impersonate/stop', [\App\Http\Controllers\ImpersonationController::class, 'destroy'])->name('impersonate.destroy');

    // Authenticated Vehicle Management Routes
    Route::prefix('vehicles')->as('vehicles.')->group(function (): void {
        Route::get('/inspection', \App\Livewire\Frontend\InspectionForm::class)->name('inspection')->middleware('can:access dashboard');
    });

    // Settings
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', \App\Livewire\Settings\Profile::class)->name('settings.profile');
    Route::get('settings/password', \App\Livewire\Settings\Password::class)->name('settings.password');
    Route::get('settings/two-factor', \App\Livewire\Settings\TwoFactor::class)->name('settings.two-factor');
    Route::get('settings/appearance', \App\Livewire\Settings\Appearance::class)->name('settings.appearance');
    Route::get('settings/locale', \App\Livewire\Settings\Locale::class)->name('settings.locale');

    // Admin
    Route::prefix('admin')->as('admin.')->group(function (): void {
        Route::get('/', \App\Livewire\Admin\Index::class)->middleware(['auth', 'verified'])->name('index')->middleware('can:access dashboard');
        Route::get('/users', \App\Livewire\Admin\Users::class)->name('users.index')->middleware('can:view users');
        Route::get('/users/create', \App\Livewire\Admin\Users\CreateUser::class)->name('users.create')->middleware('can:create users');
        Route::get('/users/{user}', \App\Livewire\Admin\Users\ViewUser::class)->name('users.show')->middleware('can:view users');
        Route::get('/users/{user}/edit', \App\Livewire\Admin\Users\EditUser::class)->name('users.edit')->middleware('can:update users');
        Route::get('/roles', \App\Livewire\Admin\Roles::class)->name('roles.index')->middleware('can:view roles');
        Route::get('/roles/create', \App\Livewire\Admin\Roles\CreateRole::class)->name('roles.create')->middleware('can:create roles');
        Route::get('/roles/{role}/edit', \App\Livewire\Admin\Roles\EditRole::class)->name('roles.edit')->middleware('can:update roles');
        Route::get('/permissions', \App\Livewire\Admin\Permissions::class)->name('permissions.index')->middleware('can:view permissions');
        Route::get('/permissions/create', \App\Livewire\Admin\Permissions\CreatePermission::class)->name('permissions.create')->middleware('can:create permissions');
        Route::get('/permissions/{permission}/edit', \App\Livewire\Admin\Permissions\EditPermission::class)->name('permissions.edit')->middleware('can:update permissions');

        // Vehicle Management
        Route::get('/vehicles', \App\Livewire\Admin\Vehicles\Index::class)->name('vehicles.index')->middleware('can:access dashboard');
        Route::get('/vehicles/create', \App\Livewire\Admin\Vehicles\CreateVehicle::class)->name('vehicles.create')->middleware('can:access dashboard');
        Route::get('/vehicles/{vehicle}/edit', \App\Livewire\Admin\Vehicles\EditVehicle::class)->name('vehicles.edit')->middleware('can:access dashboard');
        Route::get('/loans', \App\Livewire\Admin\Loans\Index::class)->name('loans.index')->middleware('can:access dashboard');
        Route::get('/inspections', \App\Livewire\Admin\Inspections\Index::class)->name('inspections.index')->middleware('can:access dashboard');
        Route::get('/expenses', \App\Livewire\Admin\Expenses\Index::class)->name('expenses.index')->middleware('can:access dashboard');

        // Meeting & Banquet Management
        Route::get('/rooms', \App\Livewire\Admin\Rooms\Index::class)->name('rooms.index')->middleware('can:access dashboard');
        Route::get('/dining-venues', \App\Livewire\Admin\DiningVenues\Index::class)->name('dining-venues.index')->middleware('can:access dashboard');
        Route::get('/meetings', \App\Livewire\Admin\Meetings\Index::class)->name('meetings.index')->middleware('can:access dashboard');
        Route::get('/meetings/create', \App\Livewire\Admin\Meetings\CreateMeeting::class)->name('meetings.create')->middleware('can:access dashboard');
        Route::get('/meetings/{meeting}/edit', \App\Livewire\Admin\Meetings\EditMeeting::class)->name('meetings.edit')->middleware('can:access dashboard');
        Route::get('/banquets', \App\Livewire\Admin\Banquets\Index::class)->name('banquets.index')->middleware('can:access dashboard');
        Route::get('/banquets/create', \App\Livewire\Admin\Banquets\CreateBanquet::class)->name('banquets.create')->middleware('can:access dashboard');
        Route::get('/banquets/{banquet}/edit', \App\Livewire\Admin\Banquets\EditBanquet::class)->name('banquets.edit')->middleware('can:access dashboard');

        // Digital Library Management
        Route::get('/books', \App\Livewire\Admin\Books\Index::class)->name('books.index')->middleware('can:access dashboard');
        Route::get('/books/create', [\App\Http\Controllers\Admin\BookController::class, 'create'])->name('books.create')->middleware('can:access dashboard');
        Route::post('/books', [\App\Http\Controllers\Admin\BookController::class, 'store'])->name('books.store')->middleware('can:access dashboard');
        Route::get('/books/{book:slug}/edit', [\App\Http\Controllers\Admin\BookController::class, 'edit'])->name('books.edit')->middleware('can:access dashboard');
        Route::put('/books/{book:slug}', [\App\Http\Controllers\Admin\BookController::class, 'update'])->name('books.update')->middleware('can:access dashboard');
        Route::delete('/books/{book:slug}', [\App\Http\Controllers\Admin\BookController::class, 'destroy'])->name('books.destroy')->middleware('can:access dashboard');

        Route::get('/categories', \App\Livewire\Admin\Categories\Index::class)->name('categories.index')->middleware('can:access dashboard');
    });
});

require __DIR__.'/auth.php';
