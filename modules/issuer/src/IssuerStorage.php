<?php

namespace Drupal\issuer;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\issuer\Entity\IssuerInterface;

/**
 * Defines the storage handler class for Issuer entities.
 *
 * This extends the base storage class, adding required special handling for
 * Issuer entities.
 *
 * @ingroup issuer
 */
class IssuerStorage extends SqlContentEntityStorage implements IssuerStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(IssuerInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {issuer_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {issuer_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(IssuerInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {issuer_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('issuer_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
