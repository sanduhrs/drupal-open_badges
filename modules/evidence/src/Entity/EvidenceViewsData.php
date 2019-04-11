<?php

namespace Drupal\evidence\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Evidence entities.
 */
class EvidenceViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    return $data;
  }

}
