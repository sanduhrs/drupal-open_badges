<?php

namespace Drupal\assertion\Entity;

use Drupal\badge\Entity\Badge;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\evidence\Entity\Evidence;
use Drupal\file\FileInterface;
use Drupal\recipient\Entity\Recipient;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Assertion entities.
 *
 * @ingroup assertion
 */
interface AssertionInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the assertion badge.
   *
   * @return \Drupal\badge\Entity\Badge
   *   Badge of the assertion.
   */
  public function getBadge();

  /**
   * Sets the assertion badge.
   *
   * @param \Drupal\badge\Entity\Badge $badge
   *   The assertion badge.
   *
   * @return \Drupal\assertion\Entity\AssertionInterface
   *   The called assertion entity.
   */
  public function setBadge(Badge $badge);

  /**
   * Gets the assertion evidence.
   *
   * @return \Drupal\evidence\Entity\Evidence
   *   Evidence of the assertion.
   */
  public function getEvidence();

  /**
   * Sets the assertion evidence.
   *
   * @param \Drupal\evidence\Entity\Evidence $evidence
   *   The assertion evidence.
   *
   * @return \Drupal\assertion\Entity\AssertionInterface
   *   The called assertion entity.
   */
  public function setEvidence(Evidence $evidence);

  /**
   * Gets the assertion image.
   *
   * @return \Drupal\file\FileInterface
   *   The image of the assertion.
   */
  public function getImage();

  /**
   * Sets the assertion image.
   *
   * @param \Drupal\file\FileInterface $image
   *   The assertion image.
   *
   * @return \Drupal\assertion\Entity\AssertionInterface
   *   The called assertion entity.
   */
  public function setImage(FileInterface $image);

  /**
   * Gets the assertion recipient.
   *
   * @return \Drupal\user\Entity\User
   *   The recipient of the assertion.
   */
  public function getRecipient();

  /**
   * Sets the assertion recipient.
   *
   * @param \Drupal\recipient\Entity\Recipient $recipient
   *   The assertion recipient.
   *
   * @return \Drupal\assertion\Entity\AssertionInterface
   *   The called assertion entity.
   */
  public function setRecipient(Recipient $recipient);

  /**
   * Returns the Assertion expiration indicator.
   *
   * @return bool
   *   TRUE if the Assertion is expired.
   */
  public function isExpired();

  /**
   * Sets the expiration date of a Assertion.
   *
   * @param string $date
   *   The Assertion expiration datetime.
   *
   * @return \Drupal\assertion\Entity\AssertionInterface
   *   The called Assertion entity.
   */
  public function setExpires($date);

  /**
   * Gets the Assertion narrative.
   *
   * @return string
   *   Narrative of the Assertion.
   */
  public function getNarrative();

  /**
   * Sets the Assertion narrative.
   *
   * @param string $narrative
   *   The Assertion narrative.
   *
   * @return \Drupal\assertion\Entity\AssertionInterface
   *   The called Assertion entity.
   */
  public function setNarrative($narrative);

  /**
   * Gets the Assertion revocation reason.
   *
   * @return string
   *   Revocation reason of the Assertion.
   */
  public function getRevocationReason();

  /**
   * Sets the Assertion revocation reason.
   *
   * @param string $reason
   *   The Assertion revocation reason.
   *
   * @return \Drupal\assertion\Entity\AssertionInterface
   *   The called Assertion entity.
   */
  public function setRevocationReason($reason);

  /**
   * Returns the Assertion revocation status indicator.
   *
   * @return bool
   *   TRUE if the Assertion is revoked.
   */
  public function isRevoked();

  /**
   * Sets the revocation status of a Assertion.
   *
   * @param bool $revoked
   *   TRUE to set this Assertion to revoked, FALSE to set it to unrevoked.
   *
   * @return \Drupal\assertion\Entity\AssertionInterface
   *   The called Assertion entity.
   */
  public function setRevocation($revoked);

  /**
   * Gets the Assertion name.
   *
   * @return string
   *   Name of the Assertion.
   */
  public function getName();

  /**
   * Sets the Assertion name.
   *
   * @param string $name
   *   The Assertion name.
   *
   * @return \Drupal\assertion\Entity\AssertionInterface
   *   The called Assertion entity.
   */
  public function setName($name);

  /**
   * Gets the Assertion creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Assertion.
   */
  public function getCreatedTime();

  /**
   * Sets the Assertion creation timestamp.
   *
   * @param int $timestamp
   *   The Assertion creation timestamp.
   *
   * @return \Drupal\assertion\Entity\AssertionInterface
   *   The called Assertion entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Assertion published status indicator.
   *
   * Unpublished Assertion are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Assertion is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Assertion.
   *
   * @param bool $published
   *   TRUE to set this Assertion to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\assertion\Entity\AssertionInterface
   *   The called Assertion entity.
   */
  public function setPublished($published);

  /**
   * Gets the Assertion revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Assertion revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\assertion\Entity\AssertionInterface
   *   The called Assertion entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Assertion revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Assertion revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\assertion\Entity\AssertionInterface
   *   The called Assertion entity.
   */
  public function setRevisionUserId($uid);

}
