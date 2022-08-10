<?php

use Illuminate\Support\Facades\Route;
use Wien\LaravelLTI\Http\Controllers\Jwks\JwksController;
use Wien\LaravelLTI\Http\Controllers\Platform\Message\OidcAuthenticationController;
use Wien\LaravelLTI\Http\Controllers\Tool\Message\OidcInitiationController;
use Wien\LaravelLTI\Http\Controllers\Platform\Service\CreateOauth2AccessTokenController;

Route::get('/lti1p3/.well-known/jwks/{keySetName}.json', JwksController::class);
Route::post('/lti1p3/auth/{keyChainIdentifier}/token', CreateOauth2AccessTokenController::class);
Route::match(['get', 'post'], '/lti1p3/oidc/authentication', OidcAuthenticationController::class);
Route::match(['get', 'post'], '/lti1p3/oidc/initiation', OidcInitiationController::class);
