<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(EventRepository $repository): Response
    {
        $currentDateTime = new \DateTime();
        $events = $repository->findAll();

        $todayEvents = [];
        $tomorrowEvents = [];
        $dayAfterTomorrowEvents = [];
        $threeDaysLaterEvents = [];
        $pastEvents = [];

        $today = (clone $currentDateTime)->setTime(0, 0);
        $tomorrow = (clone $today)->modify('+1 day');
        $dayAfterTomorrow = (clone $today)->modify('+2 days');
        $threeDaysLater = (clone $today)->modify('+3 days');

        foreach ($events as $event) {
            $eventDateTime = $event->getDateTime();

            if ($eventDateTime < $currentDateTime) {
                $pastEvents[] = $event;
            } elseif ($eventDateTime >= $today && $eventDateTime < $tomorrow) {
                $todayEvents[] = $event;
            } elseif ($eventDateTime >= $tomorrow && $eventDateTime < $dayAfterTomorrow) {
                $tomorrowEvents[] = $event;
            } elseif ($eventDateTime >= $dayAfterTomorrow && $eventDateTime < $threeDaysLater) {
                $dayAfterTomorrowEvents[] = $event;
            } elseif ($eventDateTime >= $threeDaysLater && $eventDateTime < (clone $threeDaysLater)->modify('+1 day')) {
                $threeDaysLaterEvents[] = $event;
            }
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'pastEvents' => $pastEvents,
            'todayEvents' => $todayEvents,
            'tomorrowEvents' => $tomorrowEvents,
            'dayAfterTomorrowEvents' => $dayAfterTomorrowEvents,
            'threeDaysLaterEvents' => $threeDaysLaterEvents,
            'currentDate' => $currentDateTime,
        ]);
    }
}