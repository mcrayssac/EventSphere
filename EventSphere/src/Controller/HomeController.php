<?php

// src/Controller/HomeController.php
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
        $currentDate = new \DateTime();
        $events = $repository->findAll();

        $todayEvents = [];
        $tomorrowEvents = [];
        $dayAfterTomorrowEvents = [];
        $threeDaysLaterEvents = [];

        $today = (clone $currentDate)->format('Y-m-d');
        $tomorrow = (clone $currentDate)->modify('+1 day')->format('Y-m-d');
        $dayAfterTomorrow = (clone $currentDate)->modify('+2 days')->format('Y-m-d');
        $threeDaysLater = (clone $currentDate)->modify('+3 days')->format('Y-m-d');

        foreach ($events as $event) {
            $eventDate = $event->getDateTime()->format('Y-m-d');
            if ($eventDate == $today) {
                $todayEvents[] = $event;
            } elseif ($eventDate == $tomorrow) {
                $tomorrowEvents[] = $event;
            } elseif ($eventDate == $dayAfterTomorrow) {
                $dayAfterTomorrowEvents[] = $event;
            } elseif ($eventDate == $threeDaysLater) {
                $threeDaysLaterEvents[] = $event;
            }
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'events' => $events,
            'todayEvents' => $todayEvents,
            'tomorrowEvents' => $tomorrowEvents,
            'dayAfterTomorrowEvents' => $dayAfterTomorrowEvents,
            'threeDaysLaterEvents' => $threeDaysLaterEvents,
            'currentDate' => new \DateTime(), // Pass current date again for use in the template
        ]);
    }
}