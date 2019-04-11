<?php

namespace Drupal\evidence;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface EvidenceStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Evidence revision IDs for a specific Evidence.
   *
   * @param \Drupal\evidence\Entity\EvidenceInterface $entity
   *   The Evidence entity.
   *
   * @return int[]
   *   Evidence revision IDs (in ascending order).
   */
  public function revisionIds(EvidenceInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Evidence author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Evidence revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\evidence\Entity\EvidenceInterface $entity
   *   The Evidence entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(EvidenceInterface $entity);

  /**
   * Unsets the language for all Evidence with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
