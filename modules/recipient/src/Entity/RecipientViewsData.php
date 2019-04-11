<?php

namespace Drupal\recipient\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Recipient entities.
 */
class RecipientViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    return $data;
  }

}
