<?php

namespace Drupal\badge\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Badge entities.
 */
class BadgeViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    return $data;
  }

}
