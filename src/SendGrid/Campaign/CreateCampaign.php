<?php

declare(strict_types=1);

namespace SendGridCampaign\SendGrid\Campaign;

use SendGrid\Mail\HtmlContent;
use SendGrid\Mail\IpPoolName;
use SendGrid\Mail\PlainTextContent;
use SendGrid\Mail\Subject;

class CreateCampaign implements \JsonSerializable
{
    private int $id;
    private string $title;
    private Subject $subject;
    private int $senderId;
    private array $listIds;
    private array $segmentIds;
    private array $categories;
    private int $suppressionGroupId;
    private string $customUnsubscribeUrl;
    private IpPoolName $ipPool;
    private HtmlContent $htmlContent;
    private PlainTextContent $plainContent;
    private string $status;

    public function __construct()
    {
        
    }

    /**
     * Return an array representing a request object to create a campaign for
     * the Twilio SendGrid Campaigns API
     *
     * @see https://docs.sendgrid.com/api-reference/campaigns-api/create-a-campaign
     */
    public function jsonSerialize(): null|array
    {
        return array_filter(
            [
                'id' => $this->id,
                'title' => $this->title,
                'subject' => $this->subject->jsonSerialize(),
                'sender_id' => $this->senderId,
                'list_ids' => $this->listIds,
                'segment_ids' => $this->segmentIds,
                'categories' => $this->categories,
                'suppression_group_id' => $this->suppressionGroupId,
                'custom_unsubscribe_url' => $this->customUnsubscribeUrl,
                'ip_pool' => $this->ipPool->jsonSerialize(),
                'html_content' => $this->htmlContent->jsonSerialize(),
                'plain_content' => $this->plainContent->jsonSerialize(),
                'status' => $this->status
            ]
        ) ?: null;
    }
}