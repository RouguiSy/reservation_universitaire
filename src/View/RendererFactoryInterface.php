<?php

declare(strict_types=1);

namespace App\View;

interface RendererFactoryInterface
{
    /**
     * Crée une instance de RendererInterface correspondant au format demandé (ex: 'html', 'json').
     *
     * @param string $format
     * @return RendererInterface
     * @throws \InvalidArgumentException si le format n'est pas supporté
     */
    public function create(string $format): RendererInterface;

    /**
     * Enregistre un résolveur ou une classe de rendu pour un format spécifique (Open/Closed Principle).
     *
     * @param string $format
     * @param callable|RendererInterface|class-string<RendererInterface> $resolver
     */
    public function register(string $format, callable|RendererInterface|string $resolver): void;
}
