<?php

namespace Drupal\recipient\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Recipient entities.
 *
 * @ingroup recipient
 */
interface RecipientInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Recipient name.
   *
   * @return string
   *   Name of the Recipient.
   */
  public function getName();

  /**
   * Sets the Recipient name.
   *
   * @param string $name
   *   The Recipient name.
   *
   * @return RecipientInterface
   *   The called Recipient entity.
   */
  public function setName($name);

  /**
   * Sets the recipient URL.
   *
   * @param string $url
   *   The recipient URL.
   *
   * @return RecipientInterface
   *   The called recipient entity.
   */
  public function setUrl($url);

  /**
   * Gets the recipient URL.
   *
   * @return string
   *   The homepage or social media profile of the entity, whether individual or
   *   institutional.
   */
  public function getUrl();

  /**
   * Sets the recipient telephone.
   *
   * @param string $telephone
   *   The recipient telephone.
   *
   * @return \Drupal\recipient\Entity\RecipientInterface
   *   The called recipient entity.
   */
  public function setTelephone($telephone);

  /**
   * Gets the recipient telephone.
   *
   * @return string
   *   A phone number for the entity. For maximum compatibility, the value
   *   should be expressed as a + and country code followed by the number with
   *   no spaces or other punctuation, like +16175551212 (E.164 format).
   */
  public function getTelephone();

  /**
   * Sets the recipient email.
   *
   * @param string $email
   *   The recipient email.
   *
   * @return \Drupal\recipient\Entity\RecipientInterface
   *   The called recipient entity.
   */
  public function setEmail($email);

  /**
   * Gets the recipient email.
   *
   * @return string
   *   Contact address for the individual or organization.
   */
  public function getEmail();

  /**
   * Gets the Recipient creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Recipient.
   */
  public function getCreatedTime();

  /**
   * Sets the Recipient creation timestamp.
   *
   * @param int $timestamp
   *   The Recipient creation timestamp.
   *
   * @return \Drupal\recipient\Entity\RecipientInterface
   *   The called Recipient entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Recipient published status indicator.
   *
   * Unpublished Recipient are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Recipient is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Recipient.
   *
   * @param bool $published
   *   TRUE to set this Recipient to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\recipient\Entity\RecipientInterface
   *   The called Recipient entity.
   */
  public function setPublished($published);

  /**
   * Gets the Recipient revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Recipient revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\recipient\Entity\RecipientInterface
   *   The called Recipient entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Recipient revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Recipient revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\recipient\Entity\RecipientInterface
   *   The called Recipient entity.
   */
  public function setRevisionUserId($uid);

}
