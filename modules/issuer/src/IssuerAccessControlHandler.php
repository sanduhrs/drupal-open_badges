<?php

namespace Drupal\issuer;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Issuer entity.
 *
 * @see \Drupal\issuer\Entity\Issuer.
 */
class IssuerAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\issuer\Entity\IssuerInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished issuer entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published issuer entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit issuer entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete issuer entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add issuer entities');
  }

}
