<?php
namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Mail
{
    private $api_key;
    private $api_key_secret;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
        $this->api_key = $this->params->get('app.mailjet_api_key');
        $this->api_key_secret= $this->params->get('app.mailjet_secret_api_key');
    }


    public function send($to_email, $to_name, $subject, $content)
    {
        $mj = new Client($this->api_key, $this->api_key_secret,true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "lestrage34@gmail.com",
                        'Name' => "La Boutique Française"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 2861761,
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