<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/gallery', 'Gallery::index');
$routes->get('/rooms/barcada-room', 'Gallery::barcadaRoom');
$routes->get('/rooms/standard-room', 'Gallery::standardRoom');
$routes->get('/rooms/bungalow', 'Gallery::bungalow');
$routes->get('/cave', 'Gallery::cave');
$routes->get('/inquiry', 'Inquiry::index');
$routes->get('/booking', 'Inquiry::booking');
$routes->get('/booking/confirmation', 'Inquiry::bookingConfirmation');
$routes->get('/reservation', 'Inquiry::reservation');
$routes->post('/inquiry/submit', 'Inquiry::submit');
$routes->post('/ratings/submit', 'Home::submitRating');

$routes->group('admin', static function ($routes) {
	$routes->get('login', 'Admin::login');
	$routes->post('authenticate', 'Admin::authenticate');
	$routes->get('logout', 'Admin::logout');
});

$routes->group('admin', ['filter' => 'adminauth'], static function ($routes) {
	$routes->get('/', 'Admin::dashboard');
	$routes->get('dashboard', 'Admin::dashboard');

	$routes->get('rooms', 'Admin::rooms');
	$routes->get('rooms/create', 'Admin::roomCreate');
	$routes->post('rooms/store', 'Admin::roomStore');
	$routes->get('rooms/edit/(:num)', 'Admin::roomEdit/$1');
	$routes->post('rooms/update/(:num)', 'Admin::roomUpdate/$1');
	$routes->post('rooms/delete/(:num)', 'Admin::roomDelete/$1');

	$routes->get('staff', 'Admin::staffUsers');
	$routes->get('staff/create', 'Admin::staffCreate');
	$routes->post('staff/store', 'Admin::staffStore');
	$routes->post('staff/delete/(:num)', 'Admin::staffDelete/$1');

	$routes->get('gallery', 'Admin::gallery');
	$routes->get('gallery/create', 'Admin::galleryCreate');
	$routes->post('gallery/store', 'Admin::galleryStore');
	$routes->get('gallery/edit/(:num)', 'Admin::galleryEdit/$1');
	$routes->post('gallery/update/(:num)', 'Admin::galleryUpdate/$1');
	$routes->post('gallery/delete/(:num)', 'Admin::galleryDelete/$1');

	$routes->get('room-gallery/barcada', 'Admin::roomGalleryList/barcada');
	$routes->post('room-gallery/barcada/delete/(:any)', 'Admin::roomGalleryDelete/barcada/$1');
	$routes->get('room-gallery/standard', 'Admin::roomGalleryList/standard');
	$routes->post('room-gallery/standard/delete/(:any)', 'Admin::roomGalleryDelete/standard/$1');
	$routes->get('room-gallery/bungalow', 'Admin::roomGalleryList/bungalow');
	$routes->post('room-gallery/bungalow/delete/(:any)', 'Admin::roomGalleryDelete/bungalow/$1');
	$routes->get('room-gallery/cave', 'Admin::roomGalleryList/cave');
	$routes->post('room-gallery/cave/delete/(:any)', 'Admin::roomGalleryDelete/cave/$1');

	$routes->get('inquiries', 'Admin::inquiries');
	$routes->get('inquiries/(:num)', 'Admin::inquiryShow/$1');
	$routes->post('inquiries/status/(:num)', 'Admin::inquiryStatus/$1');

	$routes->get('ratings', 'Admin::ratings');
	$routes->post('ratings/delete/(:num)', 'Admin::ratingDelete/$1');
});
