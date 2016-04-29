<?php

namespace Drupal\courier_slack\Plugin\IdentityChannel\SlackMessage;

use Drupal\courier\Plugin\IdentityChannel\IdentityChannelPluginInterface;
use Drupal\courier\ChannelInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\courier\Exception\IdentityException;

/**
 * Supports core user entities.
 *
 * @IdentityChannel(
 *   id = "identity:user:slack",
 *   label = @Translation("Drupal user to Slack message"),
 *   channel = "courier_slack_message",
 *   identity = "user",
 *   weight = 10
 * )
 */
class UserSlackMessage implements IdentityChannelPluginInterface {

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\courier_slack\Entity\SlackMessageInterface $message
   * @param \Drupal\user\UserInterface $identity
   */
  public function applyIdentity(ChannelInterface &$message, EntityInterface $identity) {
    // This would be replaced by some kind of field association.
    $message->setUsername($identity->getAccountName());
//      throw new IdentityException('User not associated with a Slack account.');
  }

}
