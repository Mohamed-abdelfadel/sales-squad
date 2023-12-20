<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\SalesmanController;
use App\Http\Controllers\TeamLeaderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/user/login', 'login');
    Route::post('/user/logout', 'logout')->middleware('auth:sanctum');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/user/logout', 'logout');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/user/profile', 'getUserProfile');
        Route::put('/user/profile/update', 'updateProfile');
    });

    Route::controller(SalesmanController::class)->group(function () {
        Route::get('/user/target', 'getTarget')->middleware('can:isSalesmanOrTeamLeader');
        Route::get('/user/leads', 'getUserLeads')->middleware('can:isSalesmanOrTeamLeader');
        Route::get('/user/leads/new', 'getUserNewLeads')->middleware('can:isSalesmanOrTeamLeader');
        Route::get('/user/leads/done', 'getUserDoneLeads')->middleware('can:isSalesmanOrTeamLeader');
        Route::get('/user/leads/lost', 'getUserLostLeads')->middleware('can:isSalesmanOrTeamLeader');
    });

    Route::controller(LeadController::class)->group(function () {
        Route::post('/leads/create', 'createLead');
        Route::put('/leads/{id}/fill', 'leadFill');
    });
});

Route::middleware(['auth:sanctum', 'can:isAdmin'])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/user/register', 'register');
    });

    Route::controller(LeadController::class)->group(function () {
        Route::get('/leads/sheet', 'getData');
        Route::get('/leads', 'getAll');
        Route::get('/leads/sync', 'syncData');
        Route::get('/leads/new', 'getNew');
        Route::get('/leads/done', 'getDone');
        Route::get('/leads/lost', 'getLost');
        Route::get('/leads/process', 'addLeadsToUsers');
        Route::get('/leads/{id}', 'getById');
    });

    Route::controller(AdminController::class)->group(function () {
        Route::get('/users', 'getUsers');
        Route::get('/user/{id}', 'getUsersById');
        Route::get('/user/{id}/target', 'getUserTarget');
        Route::put('/user/{id}/target/set', 'setTarget');
        Route::put('/user/{id}/update', 'update');
        Route::put('/user/{id}/role/set', 'setUserRole');
        Route::delete('/user/{id}/delete', 'delete');
    });
});

Route::middleware(['auth:sanctum', 'can:isTeamLeader'])->group(function () {
    Route::controller(TeamLeaderController::class)->group(function () {
        Route::get('/team/assign', 'assignSalesmanAutomatically');
        Route::get('/teams', 'getAll');
        Route::post('/team/create', 'create');
        Route::put('/team/assign/user', 'assignSalesman');
        Route::put('/team/fire/user', 'fireSalesman');
        Route::put('/team/update', 'update');
        Route::delete('/team/delete', 'delete');
    });
});
