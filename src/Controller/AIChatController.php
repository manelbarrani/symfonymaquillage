<?php

// src/Controller/BeautyAdviceController.php
namespace App\Controller;

use App\Service\AIChatService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AIChatController extends AbstractController
{
    #[Route('/beauty-advice', name: 'app_beauty_advice')]
    public function advice(Request $request, AIChatService $aiService): JsonResponse
    {
        $question = $request->request->get('question');
        if (!$question) {
            return new JsonResponse(['error' => 'Missing question'], 400);
        }

        $answer = $aiService->getAdvice($question);

        return new JsonResponse(['answer' => $answer]);
    }
}
