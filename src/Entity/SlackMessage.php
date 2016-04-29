<?php

namespace Drupal\courier_slack\Entity;

use Drupal\courier\ChannelBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines storage for a Slack compatible message.
 *
 * @ContentEntityType(
 *   id = "courier_slack_message",
 *   label = @Translation("Slack message"),
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\courier_chatroom\Form\Entity\SlackMessageForm",
 *       "add" = "Drupal\courier_chatroom\Form\Entity\SlackMessageForm",
 *       "edit" = "Drupal\courier_chatroom\Form\Entity\SlackMessageForm",
 *       "delete" = "Drupal\courier_chatroom\Form\Entity\SlackMessageForm",
 *     },
 *   },
 *   base_table = "courier_slack_message",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/courier_slack/{courier_slack_message}/edit",
 *     "edit-form" = "/courier_slack/{courier_slack_message}/edit",
 *     "delete-form" = "/courier_slack/{courier_slack_message}/delete",
 *   }
 * )
 */
class SlackMessage extends ChannelBase implements SlackMessageInterface {

  /**
   * {@inheritdoc}
   */
  public function getEndpoint() {
    return $this->get('endpoint')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setEndpoint(SlackEndpointInterface $endpoint) {
    $this->set('endpoint', ['entity' => $endpoint]);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChannel() {
    return $this->get('channel')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setChannel(SlackChannelInterface $channel) {
    $this->set('channel', ['entity' => $channel]);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUsername() {
    return $this->get('username')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setUsername($username) {
    $this->set('username', ['value' => $username]);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getMessage() {
    return $this->get('message')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setMessage($message) {
    $this->set('message', ['value' => $message]);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  static public function sendMessages(array $messages, $options = []) {
    /** @var \Drupal\courier_slack\SlackManagerInterface $slack_manager */
    $slack_manager = \Drupal::service('courier_slack.manager');

    /** @var static $message */
    foreach ($messages as $message) {
      if (!empty($message->getUsername())) {
        $slack_manager->sendToUser($message->getEndpoint(), $message->getUsername(), $message->getMessage());
      }
      if (!empty($message->getChannel())) {
        $slack_manager->sendToChannel($message->getEndpoint(), $message->getChannel(), $message->getMessage());
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function applyTokens() {
    $tokens = $this->getTokenValues();
    $this->setMessage(\Drupal::token()->replace($this->getMessage(), $tokens));
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  function isEmpty() {
    return empty($this->getMessage());
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['endpoint'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Endpoint'))
      ->setSetting('target_type', 'courier_slack_endpoint');

    $fields['username'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Username'))
      ->setDescription(t('Username'));

    $fields['channel'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Channel'))
      ->setSetting('target_type', 'courier_slack_channel');

    $fields['message'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Message'))
      ->setDescription(t('The message.'))
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 50,
        'settings' => array(
          'rows' => 3,
        ),
      ]);

    return $fields;
  }

}
