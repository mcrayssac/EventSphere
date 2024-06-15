<?php

namespace App\Controller;

use App\Service\MailerService;
use App\Entity\Event;
use App\Entity\Subscription;
use App\Form\PaymentFormType;
use App\Service\SubscriptionService;
use App\Repository\SubscriptionRepository;
use App\Service\EventService;
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class SubscriptionController extends AbstractController
{
    private $subscriptionService;
    private $mailerService;
    private $eventService;
    private $paymentService;

    public function __construct(SubscriptionService $subscriptionService, MailerService $mailerService, EventService $eventService, PaymentService $paymentService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->mailerService = $mailerService;
        $this->eventService = $eventService;
        $this->paymentService = $paymentService;
    }

    #[Route('/event/subscribe/{id}', name: 'event_subscribe')]
    public function subscribe(Event $event, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Check if the event is paid
        if ($event->getCost() > 0) {
            $form = $this->createForm(PaymentFormType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Process the payment
                $paymentResult = $this->paymentService->processPayment($user, $event, $form->getData());

                if ($paymentResult['status'] === 'succeeded') {
                    $subscription = new Subscription();
                    $subscription->setUser($user);
                    $subscription->setEvent($event);
                    $subscription->setPaymentStatus('succeeded');
                    $entityManager->persist($subscription);
                    $entityManager->flush();

                    // Send confirmation email
                    $htmlContent = $this->renderView('emails/subscription.html.twig', [
                        'firstName' => $user->getFirstName(),
                        'eventTitle' => $event->getTitle(),
                        'eventDateTime' => $event->getDateTime(),
                        'maxParticipants' => $event->getMaxParticipants(),
                    ]);
                    $this->mailerService->sendEmail(
                        $user->getEmail(),
                        $user->getFirstName(),
                        'Event Subscription Confirmation',
                        $htmlContent
                    );

                    $this->addFlash('success', 'You have successfully subscribed to the event.');
                    return $this->redirectToRoute('app_event_detail', ['id' => $event->getId()]);
                } else {
                    $this->addFlash('error', 'Payment failed. Please try again.');
                }
            }

            return $this->render('subscription/pay.html.twig', [
                'event' => $event,
                'paymentForm' => $form->createView(),
            ]);
        }

        $result = $this->subscriptionService->subscribe($user, $event);

        switch ($result) {
            case 'success':
                $htmlContent = $this->renderView('emails/subscription.html.twig', [
                    'firstName' => $user->getFirstName(),
                    'eventTitle' => $event->getTitle(),
                    'eventDateTime' => $event->getDateTime(),
                    'maxParticipants' => $event->getMaxParticipants(),
                ]);
                $this->mailerService->sendEmail(
                    $user->getEmail(),
                    $user->getFirstName(),
                    'Event Subscription Confirmation',
                    $htmlContent
                );

                $this->addFlash('success', 'You have successfully subscribed to the event.');
                break;
            case 'event_passed':
                $this->addFlash('error', 'Unable to subscribe to the event. The event has already passed.');
                break;
            case 'max_participants':
                $this->addFlash('error', 'Unable to subscribe to the event. The maximum number of participants has been reached.');
                break;
            case 'already_subscribed':
                $this->addFlash('error', 'Unable to subscribe to the event. You are already subscribed.');
                break;
            default:
                $this->addFlash('error', 'Unable to subscribe to the event. An unknown error occurred.');
                break;
        }

        return $this->redirectToRoute('app_event_detail', ['id' => $event->getId()]);
    }

    #[Route('/event/unsubscribe/{id}', name: 'event_unsubscribe')]
    public function unsubscribe(Event $event, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($this->subscriptionService->unsubscribe($user, $event)) {
            $htmlContent = $this->renderView('emails/unsubscription.html.twig', [
                'firstName' => $user->getFirstName(),
                'eventTitle' => $event->getTitle(),
            ]);
            $this->mailerService->sendEmail(
                $user->getEmail(),
                $user->getFirstName(),
                'Event Unsubscription Confirmation',
                $htmlContent
            );

            $this->addFlash('success', 'You have successfully unsubscribed from the event ' . $event->getTitle() . '.');
        } else {
            $this->addFlash('error', 'Unable to unsubscribe from the event. You might not be subscribed.');
        }

        return $this->redirectToRoute('app_event_detail', ['id' => $event->getId()]);
    }

    #[Route('/my-events', name: 'user_events')]
    public function showUserEvents(SubscriptionRepository $subscriptionRepository, EventService $eventService): Response
    {
        $user = $this->getUser();
        $subscriptions = $subscriptionRepository->findBy(['user' => $user]);
        $events = array_map(fn($subscription) => $subscription->getEvent(), $subscriptions);

        foreach ($events as $event) {
            $event->remainingPlaces = $eventService->getRemainingPlaces($event);
        }    

        return $this->render('subscription/index.html.twig', [
            'events' => $events,
        ]);
    }
}
