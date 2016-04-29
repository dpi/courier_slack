<?php

namespace Drupal\courier_slack\Form\Entity;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SlackEndpointForm extends EntityForm {

  /**
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQueryFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct(QueryFactory $query_factory) {
    $this->entityQueryFactory = $query_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity.query'));
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $endpoint = $this->entity;

    if (!$endpoint->isNew()) {
      $form['#title'] = $this->t('Edit endpoint %label', [
        '%label' => $endpoint->label(),
      ]);
    }

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $endpoint->label(),
      '#required' => TRUE,
    ];

    $form['id'] = array(
      '#type' => 'machine_name',
      '#title' => $this->t('Machine name'),
      '#default_value' => $endpoint->id(),
      '#machine_name' => array(
        'exists' => array($this, 'exists'),
        'replace_pattern' => '([^a-z0-9_]+)|(^custom$)',
        'error' => 'The machine-readable name must be unique, and can only contain lowercase letters, numbers, and underscores.',
      ),
      '#disabled' => !$endpoint->isNew(),
    );

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => t('Description'),
      '#default_value' => $endpoint->getDescription(),
    ];

    $form['persona'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Persona'),
      '#description' => $this->t('Name of your bot.'),
      '#maxlength' => 120,
      '#default_value' => $endpoint->getPersona(),
      '#required' => TRUE,
      '#placeholder' => $this->t('Bot'),
    ];

    $form['token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Team API token'),
      '#description' => $this->t('Create a new bot at https://slack.com/apps/build/custom-integration'),
      '#maxlength' => 255,
      '#default_value' => $endpoint->getToken(),
      '#required' => TRUE,
      '#placeholder' => 'XXXX-XXXXXXXXXX-XXXXXXXXXX-XXXXXXXXXX-XXXXXXXXXXXX',
    ];

    return $form;
  }

  /**
   * Callback for `id` form element.
   */
  public function exists($entity_id, array $element, FormStateInterface $form_state) {
    $query = $this->entityQueryFactory->get('courier_slack_endpoint');
    return (bool) $query->condition('id', $entity_id)->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $endpoint = $this->getEntity();
    $status = $endpoint->save();

    if ($status == SAVED_UPDATED) {
      $message = $this->t('Endpoint was updated.');
    }
    else {
      $message = $this->t('Endpoint was added.');
    }
    drupal_set_message($message);
    $this->logger('courier_slack')
      ->notice($message);

    $form_state->setRedirectUrl(Url::fromRoute('courier_slack.courier_slack_endpoint.overview'));
  }

}
