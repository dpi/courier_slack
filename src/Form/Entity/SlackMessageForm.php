<?php

namespace Drupal\courier_slack\Form\Entity;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\courier\CourierTokenElementTrait;
use Drupal\courier\Entity\TemplateCollection;
use Drupal\courier_slack\Entity\SlackMessageInterface;

/**
 * Form controller for Slack message.
 */
class SlackMessageForm extends ContentEntityForm {

  use CourierTokenElementTrait;

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state, SlackMessageInterface $slack_message = NULL) {
    $form = parent::form($form, $form_state);

    /** @var SlackMessageInterface $slack_message */
    $slack_message = $this->entity;

    if (!$slack_message->isNew()) {
      $form['#title'] = $this->t('Edit Slack message');
    }

    $template_collection = TemplateCollection::getTemplateCollectionForTemplate($slack_message);
    $form['tokens'] = $this->templateCollectionTokenElement($template_collection);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $message = $this->entity;
    $is_new = $message->isNew();
    $message->save();

    $t_args = array('%label' => $message->label());
    if ($is_new) {
      drupal_set_message(t('Slack message has been created.', $t_args));
    }
    else {
      drupal_set_message(t('Slack message was updated.', $t_args));
    }
  }

}
