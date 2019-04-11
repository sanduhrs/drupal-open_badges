<?php

namespace Drupal\issuer\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Issuer entities.
 */
class IssuerViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    return $data;
  }

}
