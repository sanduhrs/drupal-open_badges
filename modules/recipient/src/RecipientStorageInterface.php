<?php

namespace Drupal\recipient;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface RecipientStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Recipient revision IDs for a specific Recipient.
   *
   * @param \Drupal\recipient\Entity\RecipientInterface $entity
   *   The Recipient entity.
   *
   * @return int[]
   *   Recipient revision IDs (in ascending order).
   */
  public function revisionIds(RecipientInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Recipient author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Recipient revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\recipient\Entity\RecipientInterface $entity
   *   The Recipient entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(RecipientInterface $entity);

  /**
   * Unsets the language for all Recipient with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
