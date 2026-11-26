<?php

declare(strict_types=1);

namespace App\Http\Common\Controller;

use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface;

final class SwaggerController
{
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly ResponseFactory $response,
    ) {}

    public function index(): ResponseInterface
    {
        return $this->response->html(<<<'HTML'
            <!doctype html>
            <html lang="zh-CN">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>ZeroOne API</title>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5.17.14/swagger-ui.css">
            </head>
            <body>
                <div id="swagger-ui"></div>
                <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5.17.14/swagger-ui-bundle.js"></script>
                <script>
                    SwaggerUIBundle({ dom_id: '#swagger-ui', url: 'swagger/openapi.json', validatorUrl: null });
                </script>
            </body>
            </html>
            HTML);
    }

    public function specification(): ResponseInterface
    {
        $file = $this->config->get('swagger.json_dir') . '/http.json';
        $specification = is_readable($file) ? file_get_contents($file) : false;
        if ($specification === false) {
            return $this->response->json(['message' => 'OpenAPI 文档尚未生成'])->withStatus(503);
        }

        return $this->response->raw($specification)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
    }
}
