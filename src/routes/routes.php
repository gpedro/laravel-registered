<?php

Route::group([
        'prefix' => 'invitations',
        'middleware' => ['web', 'auth']
    ], function() {
    $invitationController = Eightfold\RegistrationManagementLaravel\Controllers\InvitationController::class;

    Route::get('/', $invitationController.'@index');
    Route::post('/', $invitationController.'@sendInvite');
    Route::post('/{invitation}', $invitationController.'@resendInvite');
});

Route::group([
        'middleware' => ['web']
    ], function() {
    $registerController = Eightfold\RegistrationManagementLaravel\Controllers\RegisterController::class;

    Route::get('register', $registerController.'@showRegistrationForm')
        ->name('register');
    Route::post('register', $registerController.'@register');
    Route::get('registered', $registerController.'@registered');
});

Route::group([
        'middleware' => ['web']
    ], function() {
    $loginController = Eightfold\RegistrationManagementLaravel\Controllers\LoginController::class;

    // Login
    Route::get('login', $loginController.'@showLoginForm')
        ->name('login');
    Route::post('login', $loginController.'@login');

    Route::get('login/patreon', $loginController.'@redirectToProvider');
    Route::get('login/patreon/callback', $loginController.'@handleProviderCallback');

    // Logout
    Route::post('logout', $loginController.'@logout')
        ->name('logout');
    Route::get('logout', function() {
        return redirect('/');
    });

    Route::get('/forgot-password', $loginController.'@showForgotPasswordForm');
    Route::post('/forgot-password', $loginController.'@processForgotPassword');

    Route::get('/reset-password', $loginController.'@showResetPasswordForm');
    Route::post('/reset-password', $loginController.'@processResetPasswordForm');
});

$userTypes = [];
if (count(config('registered.user_types')) == 0) {
    $userTypes = Eightfold\RegistrationManagementLaravel\Models\UserType::userTypesForRoutes();

} else {
    $userTypes = config('registered.user_types');

}
foreach ($userTypes as $userPrefix) {
    $prefix = $userPrefix['slug'];

    // User type lists.
    Route::group([
        'middleware' => ['web'],
        'prefix' => $prefix
    ], function() {
        $usersController = Eightfold\RegistrationManagementLaravel\Controllers\UsersController::class;

        Route::get('/', $usersController.'@index');
    });

    // Managing emails.
    Route::group([
        'prefix' => $prefix .'/{username}/account/emails',
        'middleware' => ['web', 'auth', 'registered-only-me']
    ], function() {
        $emailsController = Eightfold\RegistrationManagementLaravel\Controllers\EmailsController::class;

        Route::post('/add', $emailsController.'@addEmailAddress');
        Route::post('/primary', $emailsController.'@makePrimary');
        Route::post('/delete', $emailsController.'@delete');
    });

    // Managing password.
    Route::group([
        'prefix' => $prefix .'/{username}/account',
        'middleware' => ['web', 'auth', 'registered-only-me']
    ], function() {
        $accountController = Eightfold\RegistrationManagementLaravel\Controllers\AccountController::class;

        Route::get('/', $accountController.'@index');
        Route::post('/update-password', $accountController.'@updatePassword');
        // Managing type.
        Route::post('/type', $accountController.'@updateType');
    });

    // Registering password.
    Route::group([
            'prefix' => $prefix .'/{username}',
            'middleware' => ['web']
        ], function() {
        $profileController = Eightfold\RegistrationManagementLaravel\Controllers\ProfileController::class;

        Route::get('/', $profileController.'@index');
        Route::get('/confirm', $profileController.'@confirm')
            ->name('user.confirmaiton');
        Route::get('/set-password', $profileController.'@showEstablishPasswordForm')
            ->name('user.showEstablishPasswordForm');
        Route::post('/set-password', $profileController.'@establishPassword')
            ->name('user.establishPassword');

        // Editing profile.
        Route::group([
                'prefix' => '/edit',
                'middleware' => ['web', 'registered-only-me']
            ], function()  use ($profileController) {
            Route::get('/', $profileController .'@showEditProfile');
            Route::post('/update-names', $profileController .'@updateProfileInformation');
        });
    });
}