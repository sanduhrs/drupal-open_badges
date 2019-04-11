<?php

namespace Drupal\evidence;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Evidence entity.
 *
 * @see \Drupal\evidence\Entity\Evidence.
 */
class EvidenceAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\evidence\Entity\EvidenceInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished evidence entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published evidence entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit evidence entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete evidence entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add evidence entities');
  }

}
