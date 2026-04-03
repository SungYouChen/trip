<?php

use Illuminate\Support\Facades\Route;



use App\Http\Controllers\TripController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItineraryDayController;
use App\Http\Controllers\FeedbackController;

Route::get('/', function() {
    if (auth()->check()) {
        return redirect()->route('home', ['user' => auth()->user()]);
    }
    return view('welcome_or_login'); 
});

Route::get('/story', function() {
    return view('welcome_or_login');
})->name('story');

Route::middleware(['auth', 'user.scope'])->group(function () {
    Route::get('/{user}', [TripController::class, 'index'])->name('home');
    Route::get('/{user}/trips', function($user) { return redirect()->route('home', ['user' => $user]); })->name('trips.index');
    Route::get('/{user}/trip/{trip}', [TripController::class, 'show'])->name('trip.show');
    Route::get('/{user}/trip/{trip}/day/{date}', [ItineraryDayController::class, 'show'])->name('day.show');
    Route::get('/{user}/trip/{trip}/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/{user}/exchange-rate', [TripController::class, 'fetchExchangeRate'])->name('trip.exchange_rate');

    // Management Routes
    Route::post('/{user}/trips', [TripController::class, 'store'])->name('trips.store');
    Route::put('/{user}/trips/{trip}', [TripController::class, 'update'])->name('trips.update');
    Route::delete('/{user}/trips/{trip}', [TripController::class, 'destroy'])->name('trips.destroy');
    Route::put('/{user}/trip/{trip}/flight', [TripController::class, 'updateFlight'])->name('trip.flight.update');
    Route::post('/{user}/trip/{trip}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::put('/{user}/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/{user}/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::post('/{user}/trip/{trip}/checklist', [TripController::class, 'addItem'])->name('checklist.store');
    Route::delete('/{user}/trip/{trip}/checklist/{id}', [TripController::class, 'deleteItem'])->name('checklist.destroy');
    Route::put('/{user}/trip/{trip}/day/{date}', [ItineraryDayController::class, 'updateDay'])->name('day.update');
    Route::post('/{user}/trip/{trip}/day/{date}/events', [ItineraryDayController::class, 'addEvent'])->name('events.store');
    Route::put('/{user}/events/{event}', [ItineraryDayController::class, 'updateEvent'])->name('events.update');
    Route::delete('/{user}/events/{event}', [ItineraryDayController::class, 'deleteEvent'])->name('events.destroy');
    Route::post('/{user}/trip/{trip}/add-day', [TripController::class, 'addDay'])->name('trip.add_day');
    Route::delete('/{user}/trip/{trip}/day/{date}', [ItineraryDayController::class, 'deleteDay'])->name('day.destroy');
    Route::post('/{user}/trip/{trip}/toggle-share', [TripController::class, 'toggleShare'])->name('trip.toggle_share');
    Route::post('/{user}/trip/{trip}/collaborators', [TripController::class, 'addCollaborator'])->name('trip.collaborators.add');
    Route::delete('/{user}/trip/{trip}/collaborators/{collaborator}', [TripController::class, 'removeCollaborator'])->name('trip.collaborators.remove');
    Route::post('/{user}/profile', [TripController::class, 'updateProfile'])->name('profile.update');
    Route::get('/{user}/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::post('/{user}/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::delete('/{user}/feedback/{feedback}', [FeedbackController::class, 'destroy'])->name('feedback.destroy');

    // Restore & Force Delete Routes
    Route::patch('/{user}/trips/{tripId}/restore', [TripController::class, 'restore'])->name('trips.restore');
    Route::delete('/{user}/trips/{tripId}/force', [TripController::class, 'forceDelete'])->name('trips.forceDelete');
    Route::patch('/{user}/expenses/{expenseId}/restore', [ExpenseController::class, 'restore'])->name('expenses.restore');
    Route::delete('/{user}/expenses/{expenseId}/force', [ExpenseController::class, 'forceDelete'])->name('expenses.forceDelete');
    Route::patch('/{user}/trip/{trip}/checklist/{id}/restore', [TripController::class, 'restoreItem'])->name('checklist.restore');
    Route::delete('/{user}/trip/{trip}/checklist/{id}/force', [TripController::class, 'forceDeleteItem'])->name('checklist.forceDelete');
    Route::patch('/{user}/trip/{trip}/day/{dayId}/restore', [ItineraryDayController::class, 'restoreDay'])->name('day.restore');
    Route::delete('/{user}/trip/{trip}/day/{dayId}/force', [ItineraryDayController::class, 'forceDeleteDay'])->name('day.forceDelete');
    Route::patch('/{user}/events/{eventId}/restore', [ItineraryDayController::class, 'restoreEvent'])->name('events.restore');
    Route::delete('/{user}/events/{eventId}/force', [ItineraryDayController::class, 'forceDeleteEvent'])->name('events.forceDelete');
});

Route::get('/shared/{token}', [TripController::class, 'indexShared'])->name('trip.index_shared');
Route::get('/shared/{token}/day/{date}', [ItineraryDayController::class, 'showShared'])->name('day.show_shared');
Route::get('/shared/{token}/expenses', [ExpenseController::class, 'indexShared'])->name('expenses.index_shared');

Route::get('/login', function() { 
    if (auth()->check()) return redirect()->route('home', ['user' => auth()->id()]);
    return redirect('/'); 
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
