<?php

namespace App\Controller;

use App\Entity\Feedback;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FeedbackController extends AbstractController
{
    #[Route('/feedback', name: 'app_feedback')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = [
            'name' => '',
            'subject' => '',
            'message' => '',
            'rating' => 5,
        ];

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('feedback_submit', (string) $request->request->get('_token'))) {
                $this->addFlash('feedback_error', 'Feedback submission was refused.');

                return $this->redirectToRoute('app_feedback');
            }

            $data = [
                'name' => trim((string) $request->request->get('name')),
                'subject' => trim((string) $request->request->get('subject')),
                'message' => trim((string) $request->request->get('message')),
                'rating' => max(1, min(5, $request->request->getInt('rating', 5))),
            ];

            if ($data['name'] === '' || $data['subject'] === '' || $data['message'] === '') {
                $this->addFlash('feedback_error', 'Please fill in every field.');
            } else {
                $feedback = new Feedback();
                $feedback->setName($data['name']);
                $feedback->setEmail($this->getUser()?->getUserIdentifier() ?? 'unknown@example.com');
                $feedback->setSubject($data['subject']);
                $feedback->setMessage($data['message']);
                $feedback->setRating($data['rating']);

                $entityManager->persist($feedback);
                $entityManager->flush();

                $this->addFlash('feedback_success', 'Thank you. Your feedback was sent.');

                return $this->redirectToRoute('app_feedback');
            }
        }

        return $this->render('feedback/index.html.twig', [
            'data' => $data,
        ]);
    }
}
