<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MessageController extends AbstractController
{
    #[Route('/messages/send', name: 'send_message', methods: ['POST'])]
public function send(Request $request, EntityManagerInterface $em): Response
{
    $data = json_decode($request->getContent(), true);
    $receiverName = $data['name'] ?? null;
    $content = $data['content'] ?? null;

    if (!$receiverName || !$content) {
        return $this->json(['error' => 'Missing data'], 400);
    }

    // Căutăm utilizatorul după nume
    $receiver = $em->getRepository(User::class)->findOneBy(['name' => $receiverName]);

    if (!$receiver) {
        return $this->json(['error' => 'Receiver not found'], 404);
    }

    $message = new Message();
    $message->setSender($this->getUser());
    $message->setReceiver($receiver);
    $message->setContent($content);
    $message->setName($receiver->getName());

    $message->setCreatedAt(new \DateTimeImmutable());

    $em->persist($message);
    $em->flush();

    return $this->json([
        'status' => 'Message sent',
        'message' => [
            'id' => $message->getId(),
            'content' => $message->getContent(),
            'sender' => [
                'id' => $message->getSender()->getId(),
                'name' => $message->getSender()->getName(),
            ],
            'receiver' => [
                'id' => $message->getReceiver()->getId(),
                'name' => $message->getReceiver()->getName(),
            ],
            'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
        ]
    ]);
}




    #[Route('/messages', name: 'list_messages', methods: ['GET'])]
public function list(EntityManagerInterface $em): Response
{
    $user = $this->getUser();

    $messages = $em->getRepository(Message::class)->createQueryBuilder('m')
        ->where('m.sender = :user OR m.receiver = :user')
        ->setParameter('user', $user)
        ->orderBy('m.createdAt', 'ASC')
        ->getQuery()
        ->getResult();

    return $this->json($messages, context: ['groups' => 'message:read']);
}





}
