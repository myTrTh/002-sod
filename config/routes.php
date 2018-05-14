<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

// app routes
$routes->add('app_index', new Route('/', array('_controller' => 'App\Controller\ContentController::list', 'type' => 'news', 'page' => 1), array('type' => 'news', 'page' => '[0-9]+')));

// error routes
$routes->add('error', new Route('/error/{errno}', array('_controller' => 'App\Controller\ErrorController::error'), array('errno' => '403|404|500')));

// auth routes
$routes->add('auth_registration', new Route('/registration', array('_controller' => 'App\Controller\AuthController::registration')));
$routes->add('auth_registration_send_email', new Route('/registration/send-email', array('_controller' => 'App\Controller\AuthController::registrationSendEmail')));
$routes->add('auth_registration_confirmation', new Route('/registration/confirmation/{token}', array('_controller' => 'App\Controller\AuthController::registrationConfirmation'), array('token' => '.+')));

$routes->add('auth_login', new Route('/login', array('_controller' => 'App\Controller\AuthController::login')));
$routes->add('auth_logout', new Route('/logout', array('_controller' => 'App\Controller\AuthController::logout')));

$routes->add('auth_reset_password', new Route('/reset-password', array('_controller' => 'App\Controller\AuthController::resetPassword')));
$routes->add('auth_reset_password_send_email', new Route('/reset-password/send-email', array('_controller' => 'App\Controller\AuthController::resetPasswordSendEmail')));
$routes->add('auth_reset_password_confirmation', new Route('/reset-password/{token}', array('_controller' => 'App\Controller\AuthController::resetPasswordConfirmation'), array('token' => '.+')));

// user routes
$routes->add('user_profile', new Route('/profile', array('_controller' => 'App\Controller\UserController::profile')));
$routes->add('user_change_password', new Route('/profile/change-password', array('_controller' => 'App\Controller\UserController::changePassword')));

// role routes
$routes->add('role_list', new Route('/roles', array('_controller' => 'App\Controller\Admin\RoleController::list')));
$routes->add('role_add', new Route('/roles/add', array('_controller' => 'App\Controller\Admin\RoleController::add')));
$routes->add('role_edit', new Route('/roles/edit/{id}', array('_controller' => 'App\Controller\Admin\RoleController::edit'), array('id' => '[0-9]+')));
$routes->add('role_permission', new Route('/roles/permission/{id}', array('_controller' => 'App\Controller\Admin\RoleController::show'), array('id' => '[0-9]+')));
$routes->add('role_delete', new Route('/roles/delete/{id}', array('_controller' => 'App\Controller\Admin\RoleController::delete'), array('id' => '[0-9]+')));

// permission routes
$routes->add('permission_list', new Route('/permissions', array('_controller' => 'App\Controller\Admin\PermissionController::list')));
$routes->add('permission_add', new Route('/permissions/add', array('_controller' => 'App\Controller\Admin\PermissionController::add')));
$routes->add('permission_edit', new Route('/permission/edit/{id}', array('_controller' => 'App\Controller\Admin\PermissionController::edit'), array('id' => '[0-9]+')));
$routes->add('permission_delete', new Route('/permissions/delete/{id}', array('_controller' => 'App\Controller\Admin\PermissionController::delete'), array('id' => '[0-9]+')));

// admin routes
$routes->add('admin_users', new Route('/admin', array('_controller' => 'App\Controller\Admin\AdminController::users')));
$routes->add('admin_user', new Route('/admin/user/{id}', array('_controller' => 'App\Controller\Admin\AdminController::user'), array('id' => '[0-9]+')));

// content routes
$routes->add('content_list', new Route('/{type}/{page}', array('_controller' => 'App\Controller\ContentController::list', 'type' => 'news', 'page' => 1), array('type' => 'news', 'page' => '[0-9]+')));
$routes->add('content_add', new Route('/{type}/add', array('_controller' => 'App\Controller\ContentController::add', 'type' => 'news')));
$routes->add('content_edit', new Route('/{type}/edit/{id}', array('_controller' => 'App\Controller\ContentController::edit', 'type' => 'news'), array('type' => 'news', 'id' => '[0-9]+')));
$routes->add('content_delete', new Route('/{type}/delete/{id}', array('_controller' => 'App\Controller\ContentController::delete', 'type' => 'news'), array('type' => 'news', 'id' => '[0-9]+')));

// guestbook routes
$routes->add('guestbook', new Route('/guestbook/{page}', array('_controller' => 'App\Controller\GuestbookController::guestbook', 'page' => 1), array('page' => '[0-9]+')));

return $routes;