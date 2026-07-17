<?php

namespace App\Data;

final readonly class AiAnalysisResult
{
    public function __construct(
        public string $answer,
        public string $category,
        public string $sentiment,
        public string $status,
    ) {}

    public static function fallback(): self
    {
        return new self(
            answer: 'Спасибо за обращение! Я получил ваше сообщение и свяжусь с вами в ближайшее время.',
            category: 'other',
            sentiment: 'unknown',
            status: 'fallback',
        );
    }
}
