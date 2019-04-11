<?php

namespace Drupal\badge\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\file\FileInterface;
use Drupal\issuer\Entity\Issuer;
use Drupal\taxonomy\Entity\Term;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Badge entities.
 *
 * @ingroup badge
 */
interface BadgeInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the badge description.
   *
   * @return string
   *   Description of the badge.
   */
  public function getDescription();

  /**
   * Sets the badge description.
   *
   * @param string $description
   *   The badge description.
   *
   * @return BadgeInterface
   *   The called badge entity.
   */
  public function setDescription($description);

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
   * Gets the badge issuer.
   *
   * @return \Drupal\issuer\Entity\Issuer
   *   The issuer of the badge.
   */
  public function getIssuer();

  /**
   * Sets the badge issuer.
   *
   * @param \Drupal\issuer\Entity\Issuer $issuer
   *   The badge issuer.
   *
   * @return \Drupal\badge\Entity\BadgeInterface
   *   The called badge entity.
   */
  public function setIssuer(Issuer $issuer);

  /**
   * Gets the badge tags.
   *
   * @return \Drupal\taxonomy\Entity\Term[]
   *   The tags of the badge.
   */
  public function getTags();

  /**
   * Sets the issuer image.
   *
   * @param \Drupal\taxonomy\Entity\Term $term
   *   The badge issuer.
   *
   * @return \Drupal\badge\Entity\BadgeInterface
   *   The called badge entity.
   */
  public function setTags(Term $term);

  /**
   * Gets the Badge criteria.
   *
   * @return string
   *   Criteria of the Badge.
   */
  public function getCriteria();

  /**
   * Sets the Badge criteria.
   *
   * @param array $criteria
   *   The Badge criteria.
   *
   * @return \Drupal\badge\Entity\BadgeInterface
   *   The called Badge entity.
   */
  public function setCriteria(array $criteria);

  /**
   * Gets the Badge alignment.
   *
   * @return string
   *   Alignment of the Badge.
   */
  public function getAlignment();

  /**
   * Sets the Badge alignment.
   *
   * @param array $alignment
   *   The Badge alignment.
   *
   * @return \Drupal\badge\Entity\BadgeInterface
   *   The called Badge entity.
   */
  public function setAlignment(array $alignment);

  /**
   * Gets the Badge name.
   *
   * @return string
   *   Name of the Badge.
   */
  public function getName();

  /**
   * Sets the Badge name.
   *
   * @param string $name
   *   The Badge name.
   *
   * @return \Drupal\badge\Entity\BadgeInterface
   *   The called Badge entity.
   */
  public function setName($name);

  /**
   * Gets the Badge creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Badge.
   */
  public function getCreatedTime();

  /**
   * Sets the Badge creation timestamp.
   *
   * @param int $timestamp
   *   The Badge creation timestamp.
   *
   * @return \Drupal\badge\Entity\BadgeInterface
   *   The called Badge entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Badge published status indicator.
   *
   * Unpublished Badge are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Badge is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Badge.
   *
   * @param bool $published
   *   TRUE to set this Badge to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\badge\Entity\BadgeInterface
   *   The called Badge entity.
   */
  public function setPublished($published);

  /**
   * Gets the Badge revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Badge revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\badge\Entity\BadgeInterface
   *   The called Badge entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Badge revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Badge revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\badge\Entity\BadgeInterface
   *   The called Badge entity.
   */
  public function setRevisionUserId($uid);

}
