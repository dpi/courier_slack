<?php

namespace Drupal\courier_slack\Entity;

use Drupal\courier\ChannelInterface;

interface SlackMessageInterface extends ChannelInterface {

  public function getEndpoint();
  public function setEndpoint(SlackEndpointInterface $endpoint);

  public function getChannel();
  public function setChannel(SlackChannelInterface $channel);

  public function getUsername();
  public function setUsername($username);

}