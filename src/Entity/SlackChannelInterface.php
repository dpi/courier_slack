<?php

namespace Drupal\courier_slack\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

interface SlackChannelInterface extends ContentEntityInterface{

  function getName();

  function getEndpoint();

}