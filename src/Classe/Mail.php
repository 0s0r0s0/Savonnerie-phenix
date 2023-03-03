<?php


namespace App\Classe;


use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    // Clé MailJet de Test
    private $api_key = "4f797d94dda1b585492873b4c3582f07";
    private $api_key_secret = "4125770bdc94077f07ffa640a0e12056";

    // changer email/nom connecté au compte d'envoi et id template
    public function send($to_email, $to_name, $subject, $content)
    {

        $mj = new Client($this->api_key, $this->api_key_secret, true,['version' => 'v3.1'] );

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "psychestrik@live.nl",
                        'Name' => "La boutique française"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 4022702,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                        ]

                ]

            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }


}