<?php

namespace Drupal\courier_slack\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines storage for a Slack compatible message.
 *
 * @ContentEntityType(
 *   id = "courier_slack_channel",
 *   label = @Translation("Slack channel"),
 *   base_table = "courier_slack_channel",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   }
 * )
 */
class SlackChannel extends ContentEntityBase implements SlackChannelInterface {

  function getName() {
    return $this->get('channel')->value;
  }

  function getEndpoint() {
    return $this->get('endpoint')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['endpoint'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Endpoint'))
      ->setSetting('target_type', 'courier_slack_endpoint');

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Label'));

    $fields['channel'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Channel name'));

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setDescription(t('A description of the channel.'));

    return $fields;
  }

}
