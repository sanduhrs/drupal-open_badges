<?php

namespace Drupal\recipient;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\recipient\Entity\RecipientInterface;

/**
 * Defines the storage handler class for Recipient entities.
 *
 * This extends the base storage class, adding required special handling for
 * Recipient entities.
 *
 * @ingroup recipient
 */
class RecipientStorage extends SqlContentEntityStorage implements RecipientStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(RecipientInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {recipient_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {recipient_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(RecipientInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {recipient_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('recipient_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
