<?php

namespace Drupal\courier_slack\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Defines an interface for the Slack message entity.
 */
interface SlackMessageInterface extends ContentEntityInterface {

  public function getEndpoint();
  public function setEndpoint(SlackEndpointInterface $endpoint);

  public function getChannel();
  public function setChannel(SlackChannelInterface $channel);

  public function getUsername();
  public function setUsername($username);

}