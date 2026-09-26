<?php

declare(strict_types=1);

use App\Controllers\PasteController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Router;
use App\Core\Response;
use App\Core\View;

require_once dirname(__DIR__) . '/bootstrap.php';

$isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

session_name((string) Config::get('app.session_name', 'pastebox_session'));
session_set_cookie_params([
    'httponly' => true,
    'secure' => $isSecure,
    'samesite' => 'Lax',
    'path' => '/',
]);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$router = new Router();

$router->get('/', [PasteController::class, 'createForm']);
$router->get('/api/stats/pastes', [PasteController::class, 'pastesStats']);
$router->post('/paste', [PasteController::class, 'store']);
$router->get('/attachment/{id}/download', [PasteController::class, 'downloadAttachment'], ['id' => '\d+']);
$router->post('/{code}/unlock', [PasteController::class, 'unlock'], ['code' => '\d{6}']);
$router->get('/{code}/qr.svg', [PasteController::class, 'qrSvg'], ['code' => '\d{6}']);
$router->get('/{code}', [PasteController::class, 'show'], ['code' => '\d{6}']);
$router->get('/404', static function (): void {
    View::render('errors.404', ['title' => 'صفحه پیدا نشد'], 404);
});
$router->get('/410', static function (): void {
    View::render('errors.410', ['title' => 'این پیست منقضی شده است'], 410);
});

try {
    $router->dispatch(new Request());
} catch (\Throwable $throwable) {
    if ((bool) Config::get('app.debug', false)) {
        Response::abort(500, nl2br(e($throwable->getMessage() . "\n" . $throwable->getTraceAsString())));
    }
    View::render('errors.500', ['title' => 'خطای داخلی سرور'], 500);
}
