<?php

namespace Drupal\evidence\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Evidence entities.
 *
 * @ingroup evidence
 */
interface EvidenceInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Evidence name.
   *
   * @return string
   *   Name of the Evidence.
   */
  public function getName();

  /**
   * Sets the Evidence name.
   *
   * @param string $name
   *   The Evidence name.
   *
   * @return \Drupal\evidence\Entity\EvidenceInterface
   *   The called Evidence entity.
   */
  public function setName($name);

  /**
   * Gets the evidence audience.
   *
   * @return string
   *   Narrative of the evidence.
   */
  public function getAudience();

  /**
   * Sets the evidence audience.
   *
   * @param string $audience
   *   The evidence audience.
   *
   * @return \Drupal\evidence\Entity\EvidenceInterface
   *   The called evidence entity.
   */
  public function setAudience($audience);

  /**
   * Gets the evidence narrative.
   *
   * @return string
   *   Narrative of the evidence.
   */
  public function getNarrative();

  /**
   * Sets the evidence narrative.
   *
   * @param string $narrative
   *   The evidence narrative.
   *
   * @return \Drupal\evidence\Entity\EvidenceInterface
   *   The called evidence entity.
   */
  public function setNarrative($narrative);

  /**
   * Gets the evidence description.
   *
   * @return string
   *   Description of the evidence.
   */
  public function getDescription();

  /**
   * Sets the evidence description.
   *
   * @param string $description
   *   The evidence description.
   *
   * @return \Drupal\evidence\Entity\EvidenceInterface
   *   The called evidence entity.
   */
  public function setDescription($description);

  /**
   * Gets the evidence URL.
   *
   * @return string
   *   URL of the evidence.
   */
  public function getUrl();

  /**
   * Sets the evidence URL.
   *
   * @param string $url
   *   The evidence URL.
   *
   * @return \Drupal\evidence\Entity\EvidenceInterface
   *   The called evidence entity.
   */
  public function setUrl($url);

  /**
   * Gets the evidence genre.
   *
   * @return string
   *   Genre of the evidence.
   */
  public function getGenre();

  /**
   * Sets the evidence genre.
   *
   * @param string $genre
   *   The evidence genre.
   *
   * @return \Drupal\evidence\Entity\EvidenceInterface
   *   The called evidence entity.
   */
  public function setGenre($genre);

  /**
   * Gets the Evidence creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Evidence.
   */
  public function getCreatedTime();

  /**
   * Sets the Evidence creation timestamp.
   *
   * @param int $timestamp
   *   The Evidence creation timestamp.
   *
   * @return \Drupal\evidence\Entity\EvidenceInterface
   *   The called Evidence entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Evidence published status indicator.
   *
   * Unpublished Evidence are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Evidence is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Evidence.
   *
   * @param bool $published
   *   TRUE to set this Evidence to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\evidence\Entity\EvidenceInterface
   *   The called Evidence entity.
   */
  public function setPublished($published);

  /**
   * Gets the Evidence revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Evidence revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\evidence\Entity\EvidenceInterface
   *   The called Evidence entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Evidence revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Evidence revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\evidence\Entity\EvidenceInterface
   *   The called Evidence entity.
   */
  public function setRevisionUserId($uid);

}
