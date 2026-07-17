<?php

namespace App\Contracts;

use App\Data\AiAnalysisResult;

interface AiAnalyzerInterface
{
    public function analyze(string $comment): AiAnalysisResult;
}
