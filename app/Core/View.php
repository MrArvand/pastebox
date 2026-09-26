<?php

declare(strict_types=1);

namespace App\Core;

use App\Services\PasteService;

final class View
{
    /**
     * @param array<string, mixed> $data
     */
    public static function render(string $view, array $data = [], int $statusCode = 200): void
    {
        http_response_code($statusCode);

        $data = array_merge(
            ['sharedPastesCount' => (new PasteService())->latestPasteId()],
            $data
        );

        $viewFile = BASE_PATH . '/app/Views/' . str_replace('.', '/', $view) . '.php';
        if (!is_file($viewFile)) {
            Response::abort(500, 'View not found.');
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();

        require BASE_PATH . '/app/Views/layouts/main.php';
    }
}
