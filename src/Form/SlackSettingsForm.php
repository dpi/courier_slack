<?php

namespace Drupal\courier_slack\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\courier_slack\Entity\SlackEndpoint;

/**
 * Configure Courier Slack settings.
 */
class SlackSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'courier_slack_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Sync channels for all endpoints'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\courier_slack\SlackManagerInterface $manager */
    $manager = \Drupal::service('courier_slack.manager');
    
    /** @var \Drupal\courier_slack\Entity\SlackEndpointInterface $endpoint */
    foreach (SlackEndpoint::loadMultiple() as $endpoint) {
      drupal_set_message($this->t('Synced channels for @endpoint', [
        '@endpoint' => $endpoint->label(),
      ]));
      $manager->syncChannels($endpoint);
    }
  }

}
