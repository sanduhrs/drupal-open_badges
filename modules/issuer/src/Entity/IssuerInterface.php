<?php

namespace Drupal\issuer\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\file\FileInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Issuer entities.
 *
 * @ingroup issuer
 */
interface IssuerInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Issuer name.
   *
   * @return string
   *   Name of the Issuer.
   */
  public function getName();

  /**
   * Sets the Issuer name.
   *
   * @param string $name
   *   The Issuer name.
   *
   * @return \Drupal\issuer\Entity\IssuerInterface
   *   The called Issuer entity.
   */
  public function setName($name);

  /**
   * Gets the Issuer description.
   *
   * @return string
   *   Description of the Issuer.
   */
  public function getDescription();

  /**
   * Sets the Issuer description.
   *
   * @param string $description
   *   The issuer description.
   *
   * @return \Drupal\badge\Entity\BadgeInterface
   *   The called badge entity.
   */
  public function setDescription($description);

  /**
   * Gets the issuer email.
   *
   * @return string
   *   Email of the issuer.
   */
  public function getEmail();

  /**
   * Sets the issuer email.
   *
   * @param string $email
   *   The issuer email.
   *
   * @return \Drupal\issuer\Entity\IssuerInterface
   *   The called issuer entity.
   */
  public function setEmail($email);

  /**
   * Gets the issuer telephone.
   *
   * @return string
   *   Telephone of the issuer.
   */
  public function getTelephone();

  /**
   * Sets the issuer telephone.
   *
   * @param string $telephone
   *   The issuer telephone.
   *
   * @return \Drupal\issuer\Entity\IssuerInterface
   *   The called issuer entity.
   */
  public function setTelephone($telephone);

  /**
   * Gets the issuer Url.
   *
   * @return string
   *   Url of the issuer.
   */
  public function getUrl();

  /**
   * Sets the issuer Url.
   *
   * @param string $url
   *   The issuer Url.
   *
   * @return \Drupal\issuer\Entity\IssuerInterface
   *   The called issuer entity.
   */
  public function setUrl($url);

  /**
   * Gets the issuer image.
   *
   * @return \Drupal\file\FileInterface
   *   The image of the assertion.
   */
  public function getImage();

  /**
   * Sets the issuer image.
   *
   * @param \Drupal\file\FileInterface $image
   *   The assertion image.
   *
   * @return \Drupal\issuer\Entity\IssuerInterface
   *   The called issuer entity.
   */
  public function setImage(FileInterface $image);

  /**
   * Gets the Issuer creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Issuer.
   */
  public function getCreatedTime();

  /**
   * Sets the Issuer creation timestamp.
   *
   * @param int $timestamp
   *   The Issuer creation timestamp.
   *
   * @return \Drupal\issuer\Entity\IssuerInterface
   *   The called Issuer entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Issuer published status indicator.
   *
   * Unpublished Issuer are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Issuer is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Issuer.
   *
   * @param bool $published
   *   TRUE to set this Issuer to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\issuer\Entity\IssuerInterface
   *   The called Issuer entity.
   */
  public function setPublished($published);

  /**
   * Gets the Issuer revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Issuer revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\issuer\Entity\IssuerInterface
   *   The called Issuer entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Issuer revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Issuer revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\issuer\Entity\IssuerInterface
   *   The called Issuer entity.
   */
  public function setRevisionUserId($uid);

}
