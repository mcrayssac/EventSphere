<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\PaymentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private $stripeSecretKey;
    private $entityManager;

    public function __construct(string $stripeSecretKey, EntityManagerInterface $entityManager)
    {
        $this->stripeSecretKey = $stripeSecretKey;
        $this->entityManager = $entityManager;
    }

    #[Route('/event/pay/{id}', name: 'event_pay')]
    public function pay(Event $event, Request $request): Response
    {
        $form = $this->createForm(PaymentFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            Stripe::setApiKey($this->stripeSecretKey);

            $paymentIntent = PaymentIntent::create([
                'amount' => $event->getCost() * 100, // cost in cents
                'currency' => 'usd',
                'payment_method_types' => ['card'],
            ]);

            return $this->render('payment/confirm.html.twig', [
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        }

        return $this->render('payment/pay.html.twig', [
            'event' => $event,
            'paymentForm' => $form->createView(),
        ]);
    }
}
