<?php

namespace Drupal\assertion\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Assertion entities.
 */
class AssertionViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    return $data;
  }

}
