<?php

namespace Drupal\courier_slack;

use CL\Slack\Payload\ChannelsListPayload;
use CL\Slack\Payload\ChatPostMessagePayload;
use CL\Slack\Payload\PayloadResponseInterface;
use CL\Slack\Transport\ApiClient;
use Drupal\courier_slack\Entity\SlackChannel;
use Drupal\courier_slack\Entity\SlackChannelInterface;
use Drupal\courier_slack\Entity\SlackEndpointInterface;

/**
 * The Slack manager.
 */
class SlackManager implements SlackManagerInterface {

  public function __construct() {
  }


  public function sendToChannel(SlackEndpointInterface $endpoint, SlackChannelInterface $channel, $message) {
    $channel = '#' . $channel->getName();
    $this->sendMessage($endpoint, $channel, $message);
  }

  public function sendToUser(SlackEndpointInterface $endpoint, $message, $username) {
    $channel = '@' . $username;
    $this->sendMessage($endpoint, $channel, $message);
  }

  protected function sendMessage(SlackEndpointInterface $endpoint, $channel, $message) {
    $client = $this->client($endpoint);
    $payload = new ChatPostMessagePayload();
    $payload->setUsername($endpoint->getPersona());
    $payload->setChannel($channel);
    $payload->setText($message);

    $response = $client->send($payload);
    if (!$response->isOk()) {
      $this->throwFailure($response, 'send');
    }
  }

  public function syncChannels(SlackEndpointInterface $endpoint) {
    $client = $this->client($endpoint);

    $payload = new ChannelsListPayload();
    $response = $client->send($payload);

    /** @var \CL\Slack\Model\Channel[] $channels */
    $channels = [];
    if ($response->isOk()) {
      /** @var \CL\Slack\Payload\ChannelsListPayloadResponse $response */
      foreach ($response->getChannels() as $channel) {
        $channels[$channel->getName()] = $channel;
      }
    }
    else {
      $this->throwFailure($response, 'channelsync');
    }

    $channel_storage = \Drupal::entityTypeManager()
      ->getStorage('courier_slack_channel');
    $query = $channel_storage->getQuery();

    $group = $query->orConditionGroup();
    foreach (array_keys($channels) as $name) {
      $group->condition('channel', $name);
    }

    $query
      ->condition('endpoint', $endpoint->id())
      ->condition($group);
    $ids = $query->execute();

    foreach ($channel_storage->loadMultiple($ids) as $channel) {
      /** @var SlackChannelInterface $channel */
      if (isset($channels[$channel->getName()])) {
        unset($channels[$channel->getName()]);
      }
    }

    // Create remaining non-existent channels.
    foreach ($channels as $name => $channel) {
      $slack_channel = SlackChannel::create([
        'channel' => $name,
        'label' => $endpoint->label() . ' ' . '#' . $name,
        'description' => $channel->getTopic(),
        'endpoint' => $endpoint->id(),
      ]);
      $slack_channel->save();
    }
  }

  /**
   * Get a client
   *
   * @param \Drupal\courier_slack\Entity\SlackEndpointInterface $endpoint
   *   A slack endpoint entity.
   *
   * @return \CL\Slack\Transport\ApiClient
   *   A client instance.
   */
  protected function client(SlackEndpointInterface $endpoint) {
    $client = new ApiClient($endpoint->getToken());
    return $client;
  }

  protected function throwFailure(PayloadResponseInterface $response, $logger_channel) {
    \Drupal::logger('courier_slack.' . $logger_channel)
      ->error('@error: @error_long', [
        '@error' => $response->getError(),
        '@error_long' => $response->getErrorExplanation(),
      ]);
    throw new \Exception($response->getErrorExplanation());
  }

}
