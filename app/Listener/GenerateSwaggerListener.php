<?php

declare(strict_types=1);

namespace App\Listener;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BeforeMainServerStart;
use Hyperf\Swagger\Generator;

#[Listener]
final class GenerateSwaggerListener implements ListenerInterface
{
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly Generator       $generator,
    ) {}

    public function listen(): array
    {
        return [BeforeMainServerStart::class];
    }

    public function process(object $event): void
    {
        if ($this->config->get('swagger.serve_documents')) {
            $this->generator->generate();
        }
    }
}
