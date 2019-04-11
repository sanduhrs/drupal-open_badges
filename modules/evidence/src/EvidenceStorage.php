<?php

namespace Drupal\evidence;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\evidence\Entity\EvidenceInterface;

/**
 * Defines the storage handler class for Evidence entities.
 *
 * This extends the base storage class, adding required special handling for
 * Evidence entities.
 *
 * @ingroup evidence
 */
class EvidenceStorage extends SqlContentEntityStorage implements EvidenceStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(EvidenceInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {evidence_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {evidence_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(EvidenceInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {evidence_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('evidence_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
