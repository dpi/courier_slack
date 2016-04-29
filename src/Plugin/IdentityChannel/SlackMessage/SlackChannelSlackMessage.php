<?php

namespace Drupal\courier_slack\Plugin\IdentityChannel\SlackMessage;

use Drupal\courier\Plugin\IdentityChannel\IdentityChannelPluginInterface;
use Drupal\courier\ChannelInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Supports Slack channels as targets for Slack messages.
 *
 * @IdentityChannel(
 *   id = "identity:slack_channel:slack",
 *   label = @Translation("Slack channel to Slack message"),
 *   channel = "courier_slack_message",
 *   identity = "courier_slack_channel",
 *   weight = 10
 * )
 */
class SlackChannelSlackMessage implements IdentityChannelPluginInterface {

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\courier_slack\Entity\SlackMessageInterface $message
   * @param \Drupal\courier_slack\Entity\SlackChannelInterface $identity
   */
  public function applyIdentity(ChannelInterface &$message, EntityInterface $identity) {
    $message->setEndpoint($identity->getEndpoint());
    $message->setChannel($identity);
  }

}
