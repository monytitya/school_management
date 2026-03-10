<?php

// Auth Routes
// POST   /api/auth/register
// POST   /api/auth/login
// GET    /api/auth/me          (protected)
// POST   /api/auth/change-password (protected)

function registerAuthRoutes(Router $router): void {
    $ctrl = new AuthController();

    $router->add('POST', '/auth/register',         fn() => $ctrl->register());
    $router->add('POST', '/auth/login',             fn() => $ctrl->login());
    $router->add('GET',  '/auth/me',                fn() => $ctrl->me());
    $router->add('POST', '/auth/change-password',   fn() => $ctrl->changePassword());
}
