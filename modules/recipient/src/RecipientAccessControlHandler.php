<?php

namespace Drupal\recipient;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Recipient entity.
 *
 * @see \Drupal\recipient\Entity\Recipient.
 */
class RecipientAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\recipient\Entity\RecipientInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished recipient entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published recipient entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit recipient entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete recipient entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add recipient entities');
  }

}
