<?php

namespace Drupal\assertion;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Assertion entity.
 *
 * @see \Drupal\assertion\Entity\Assertion.
 */
class AssertionAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\assertion\Entity\AssertionInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished assertion entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published assertion entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit assertion entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete assertion entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add assertion entities');
  }

}
