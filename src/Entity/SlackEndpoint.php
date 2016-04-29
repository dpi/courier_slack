<?php

namespace Drupal\courier_slack\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * @ConfigEntityType(
 *   id = "courier_slack_endpoint",
 *   label = @Translation("Slack endpoint"),
 *   admin_permission = "administer courier",
 *   config_prefix = "slack",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   handlers = {
 *     "list_builder" = "\Drupal\courier_slack\SlackEndpointListBuilder",
 *     "form" = {
 *       "add" = "Drupal\courier_slack\Form\Entity\SlackEndpointForm",
 *       "edit" = "Drupal\courier_slack\Form\Entity\SlackEndpointForm",
 *       "delete" = "Drupal\courier_slack\Form\Entity\SlackEndpointDeleteForm",
 *     },
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/courier_slack/manage/{courier_slack_endpoint}",
 *     "edit-form" = "/admin/structure/courier_slack/manage/{courier_slack_endpoint}",
 *     "delete-form" = "/admin/structure/courier_slack/manage/{courier_slack_endpoint}/delete",
 *   }
 * )
 */
class SlackEndpoint extends ConfigEntityBase implements SlackEndpointInterface {

  protected $id;
  protected $label;
  protected $description;
  protected $token;
  protected $persona;

  public function getDescription() {
    return $this->description;
  }

  public function getToken() {
    return $this->token;
  }

  public function getPersona() {
    return $this->persona;
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);
    $entity_type_manager = \Drupal::entityTypeManager();
    $channel_storage = $entity_type_manager->getStorage('courier_slack_channel');
    $message_storage = $entity_type_manager->getStorage('courier_slack_message');
    // Delete associated messages and channels.
    foreach ($entities as $entity) {
      $channels = $channel_storage
        ->loadByProperties(['endpoint' => $entity->id()]);
      $channel_storage->delete($channels);
      $messages = $message_storage
        ->loadByProperties(['endpoint' => $entity->id()]);
      $message_storage->delete($messages);
    }
  }

}
