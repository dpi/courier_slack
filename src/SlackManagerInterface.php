<?php

namespace Drupal\courier_slack;

use Drupal\courier_slack\Entity\SlackEndpointInterface;
use Drupal\courier_slack\Entity\SlackChannelInterface;

/**
 * Interface for Slack manager.
 */
interface SlackManagerInterface {

  public function sendToChannel(SlackEndpointInterface $endpoint, SlackChannelInterface $channel, $message);
  public function sendToUser(SlackEndpointInterface $endpoint, $username, $message);
  public function syncChannels(SlackEndpointInterface $endpoint);

}
