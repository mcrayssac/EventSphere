<?php

namespace App\Controller;

use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebhookController extends AbstractController
{
    private $stripeWebhookSecret;
    private $entityManager;

    public function __construct(string $stripeWebhookSecret, EntityManagerInterface $entityManager)
    {
        $this->stripeWebhookSecret = $stripeWebhookSecret;
        $this->entityManager = $entityManager;
    }

    #[Route('/webhook/stripe', name: 'webhook_stripe')]
    public function stripeWebhook(Request $request): Response
    {
        $payload = @file_get_contents('php://input');
        $sig_header = $request->headers->get('stripe-signature');

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $this->stripeWebhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Invalid signature', 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentSucceeded($paymentIntent);
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentFailed($paymentIntent);
                break;
            default:
                return new Response('Unhandled event type', 400);
        }

        return new Response('Success', 200);
    }

    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        $subscription = $this->entityManager->getRepository(Subscription::class)->findOneBy([
            'user' => $paymentIntent->metadata->user_id,
            'event' => $paymentIntent->metadata->event_id,
        ]);

        if ($subscription) {
            $subscription->setPaymentStatus('succeeded');
            $this->entityManager->flush();
        }
    }

    private function handlePaymentIntentFailed($paymentIntent)
    {
        $subscription = $this->entityManager->getRepository(Subscription::class)->findOneBy([
            'user' => $paymentIntent->metadata->user_id,
            'event' => $paymentIntent->metadata->event_id,
        ]);

        if ($subscription) {
            $subscription->setPaymentStatus('failed');
            $this->entityManager->flush();
        }
    }
}
