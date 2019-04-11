<?php

namespace Drupal\badge;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface BadgeStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Badge revision IDs for a specific Badge.
   *
   * @param \Drupal\badge\Entity\BadgeInterface $entity
   *   The Badge entity.
   *
   * @return int[]
   *   Badge revision IDs (in ascending order).
   */
  public function revisionIds(BadgeInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Badge author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Badge revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\badge\Entity\BadgeInterface $entity
   *   The Badge entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(BadgeInterface $entity);

  /**
   * Unsets the language for all Badge with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
