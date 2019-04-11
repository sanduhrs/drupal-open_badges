<?php

namespace Drupal\badge;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\badge\Entity\BadgeInterface;

/**
 * Defines the storage handler class for Badge entities.
 *
 * This extends the base storage class, adding required special handling for
 * Badge entities.
 *
 * @ingroup badge
 */
class BadgeStorage extends SqlContentEntityStorage implements BadgeStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(BadgeInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {badge_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {badge_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(BadgeInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {badge_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('badge_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
