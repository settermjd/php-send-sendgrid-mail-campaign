<?php

declare(strict_types=1);

use SendGrid\Response;

require_once('vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function getCampaignId(Response $response): string
{
    $body = \json_decode($response->body());
    return (string)$body->id;
}

$sendGrid = new \SendGrid($_ENV['SENDGRID_API_KEY']);

try {
    $response = $sendGrid
        ->client
        ->marketing()
        ->singlesends()
        ->post([
            'name' => 'New Products',
            'send_to' => [
                'list_ids' => [$_ENV['SENDGRID_LIST_ID']]
            ],
            'email_config' => [
                'subject' => 'New Products for Summer!',
                'html_content' => '<html><body>Welcome To Our Summer Sale!</body></html>',
                'generate_plain_content' => true,
                'custom_unsubscribe_url' => 'https://matthewsetter.com/unsubscribe',
                'sender_id' => 1409812,
            ],
        ]);
} catch (Exception $ex) {
    printf('Failed to create the single send, because: %s',  $ex->getMessage());
    exit -1;
}

if ($response->statusCode() !== 201) {
    printf(
        "Request failed. Code: %d Reason: %s",
        $response->statusCode(),
        $response->body()
    );
    exit(-1);
}

try {
    $sendAt = (new \DateTime())
        ->add(new \DateInterval('PT2M'))
        ->format(\DateTimeInterface::ATOM);
    $response = $sendGrid
        ->client
        ->marketing()
        ->singlesends()
        ->_(getCampaignId($response))
        ->schedule()
        ->put(['send_at' => $sendAt]);
} catch (Exception $ex) {
    echo 'Caught exception: ' . $ex->getMessage();
    exit -1;
}

switch ($response->statusCode()) {
    case 200:
    case 201:
        echo "Campaign successfully scheduled.";
        exit(0);
        break;
    case 404:
    case 500:
    default:
        printf(
            "Couldn't schedule the campaign. Code: %d. Reason: %s",
            $response->statusCode(),
            $response->body()
        );
        exit -1;
}