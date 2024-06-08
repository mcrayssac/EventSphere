<?php

namespace App\Service;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Component\HttpFoundation\Response;

class MailerService
{
    private $mailjet;

    public function __construct(string $mailjetApiKey, string $mailjetApiSecret)
    {
        $this->mailjet = new Client($mailjetApiKey, $mailjetApiSecret, true, ['version' => 'v3.1']);
    }

    public function sendEmail(string $toEmail, string $toName, string $subject, string $htmlContent): bool
    {
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "noreplyeventsphere@gmail.com",
                        'Name' => "Event Sphere"
                    ],
                    'To' => [
                        [
                            'Email' => $toEmail,
                            'Name' => $toName
                        ]
                    ],
                    'Subject' => $subject,
                    'HTMLPart' => $htmlContent,
                ]
            ]
        ];

        $response = $this->mailjet->post(Resources::$Email, ['body' => $body]);

        return $response->success();
    }
}
