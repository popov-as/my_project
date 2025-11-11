<?php
// src/Controller/BlogController.php
namespace App\Controller;

use App\Service\MessageGenerator;
use App\Dto\BlogDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class BlogController extends AbstractController
{
    #[Route('/blog/{text}', methods: ['GET', 'HEAD'], name: 'blog_list')]
    public function list(string $text, MessageGenerator $messageGenerator): Response
    {
        $message = $messageGenerator->getHappyMessage();

        return $this->render('lucky/message.html.twig', [
            'message' => $message,
            'number' => $text
        ]);
    }

    #[Route('/blog/post', methods: ['POST'], name: 'blog_post', format: 'json')]
    public function create(#[MapRequestPayload] BlogDto $blogDto): JsonResponse
    {
        return $this->json(['myObject' => $blogDto]);
    }
}