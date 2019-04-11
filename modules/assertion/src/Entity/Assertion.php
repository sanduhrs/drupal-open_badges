<?php

namespace Drupal\assertion\Entity;

use Drupal;
use Drupal\badge\Entity\Badge;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Url;
use Drupal\evidence\Entity\Evidence;
use Drupal\file\FileInterface;
use Drupal\recipient\Entity\Recipient;
use Drupal\user\UserInterface;
use PNG\Image;

/**
 * Defines the Assertion entity.
 *
 * @ingroup assertion
 *
 * @ContentEntityType(
 *   id = "assertion",
 *   label = @Translation("Assertion"),
 *   handlers = {
 *     "storage" = "Drupal\assertion\AssertionStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\assertion\AssertionListBuilder",
 *     "views_data" = "Drupal\assertion\Entity\AssertionViewsData",
 *     "translation" = "Drupal\assertion\AssertionTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\assertion\Form\AssertionForm",
 *       "add" = "Drupal\assertion\Form\AssertionForm",
 *       "edit" = "Drupal\assertion\Form\AssertionForm",
 *       "delete" = "Drupal\assertion\Form\AssertionDeleteForm",
 *     },
 *     "access" = "Drupal\assertion\AssertionAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\assertion\AssertionHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "assertion",
 *   data_table = "assertion_field_data",
 *   revision_table = "assertion_revision",
 *   revision_data_table = "assertion_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer assertion entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/assertion/{assertion}",
 *     "add-form" = "/admin/content/assertion/add",
 *     "edit-form" = "/admin/content/assertion/{assertion}/edit",
 *     "delete-form" = "/admin/content/assertion/{assertion}/delete",
 *     "version-history" = "/admin/content/assertion/{assertion}/revisions",
 *     "revision" = "/admin/content/assertion/{assertion}/revisions/{assertion_revision}/view",
 *     "revision_revert" = "/admin/content/assertion/{assertion}/revisions/{assertion_revision}/revert",
 *     "revision_delete" = "/admin/content/assertion/{assertion}/revisions/{assertion_revision}/delete",
 *     "translation_revert" = "/admin/content/assertion/{assertion}/revisions/{assertion_revision}/revert/{langcode}",
 *     "collection" = "/admin/content/assertion",
 *   },
 *   field_ui_base_route = "assertion.settings"
 * )
 */
class Assertion extends RevisionableContentEntityBase implements AssertionInterface {

  use EntityChangedTrait;

  const ALGO = 'sha256';

  /**
   * The salt string.
   *
   * @var string
   */
  protected $salt;

  /**
   * Hashing algorithm.
   *
   * @param string $data
   *   The data to be hashed.
   *
   * @return string
   *   The hashed string.
   */
  public function hash($data) {
    return static::ALGO . '$' . hash(static::ALGO, $data . $this->salt);
  }

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    // Prepare the destination directory.
    $file_directory = 'public://' . $this->getFieldDefinition('field_image')
      ->getSetting('file_directory');
    $file_directory = Drupal::token()->replace($file_directory);
    file_prepare_directory($file_directory, FILE_CREATE_DIRECTORY);

    // Get a copy of the to be asserted badge image.
    $image = file_copy(
      $this->getBadge()->getImage(),
      $file_directory . '/' . $this->getBadge()->getImage()->getFilename(),
      FILE_EXISTS_REPLACE
    );
    // Add or replace the assertion image.
    $this->setImage($image);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the assertion owner
    // the revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    $this->salt = "deadsee";

    $badge = $this->getBadge();
    $recipient = $this->getRecipient();

    $open_badges_assertion = [
      "@context" => "https://w3id.org/openbadges/v2",
      "id" => Url::fromRoute('entity.assertion.canonical', ['assertion' => $this->id(), '_format' => 'json'], ['absolute' => TRUE])->toString(),
      "type" => "Assertion",
      "recipient" => [
        "type" => "email",
        "identity" => $this->hash($recipient->getEmail()),
        "hashed" => TRUE,
        "hash" => $this->salt,
      ],
      "issuedOn" => date('c', $this->getCreatedTime()),
      "verification" => [
        "type" => "hosted",
      ],
      "badge" => [
        "id" => Url::fromRoute('entity.badge.canonical', ['badge' => $badge->id(), '_format' => 'json'], ['absolute' => TRUE])->toString(),
        "type" => "BadgeClass",
        "name" => $badge->getName(),
        "description" => $badge->getDescription(),
        "image" => $badge->getImage(),
        "issuer" => [
          "id" => Url::fromRoute('entity.issuer.canonical', ['issuer' => $badge->getIssuer()->id(), '_format' => 'json'], ['absolute' => TRUE])->toString(),
          "type" => "Profile",
          "name" => $badge->getIssuer()->getName(),
          "url" => $badge->getIssuer()->getUrl(),
          "email" => $badge->getIssuer()->getEmail(),
        ],
      ],
    ];

    $png = new Image();
    $data = file_get_contents($this->getBadge()->getImage()->getFileUri());
    $png->setContents($data);
    $png->addITXtChunk(
      'openbadges',
      'en',
      str_replace('"@type":', '"type":', json_encode($open_badges_assertion))
    );
    file_save_data(
      $png->getContents(),
      $this->getImage()->getFileUri(),
      FILE_EXISTS_REPLACE
    );
    parent::postSave($storage, $update);
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getBadge() {
    /** @var \Drupal\Core\Field\EntityReferenceFieldItemList $target */
    $target = $this->get('field_badge')->first()->getValue();
    return Badge::load($target['target_id']);
  }

  /**
   * {@inheritdoc}
   */
  public function setBadge(Badge $badge) {
    $this->set('field_badge', $badge->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEvidence() {
    /** @var \Drupal\Core\Field\EntityReferenceFieldItemList $target */
    $target = $this->get('field_evidence')->first()->getValue();
    return Evidence::load($target['target_id']);
  }

  /**
   * {@inheritdoc}
   */
  public function setEvidence(Evidence $evidence) {
    $this->set('field_evidence', $evidence->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getImage() {
    /** @var \\Drupal\Core\Field\EntityReferenceFieldItemList $target */
    $target = $this->get('field_image')->first()->getValue();
    $file = Drupal::entityTypeManager()->getStorage('file')
      ->load($target['target_id']);
    return $file;
  }

  /**
   * {@inheritdoc}
   */
  public function setImage(FileInterface $image) {
    $this->set('field_image', $image->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRecipient() {
    /** @var \Drupal\Core\Field\EntityReferenceFieldItemList $target */
    $target = $this->get('field_recipient')->first()->getValue();
    return Recipient::load($target['target_id']);
  }

  /**
   * {@inheritdoc}
   */
  public function setRecipient(Recipient $recipient) {
    $this->set('field_recipient', $recipient->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isExpired() {
    // TODO: Implement isExpired() method.
  }

  /**
   * {@inheritdoc}
   */
  public function setExpires($date) {
    // TODO: Implement setExpires() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getNarrative() {
    return $this->get('field_narrative')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setNarrative($narrative) {
    $this->set('field_narrative', $narrative);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevocationReason() {
    return $this->get('field_revocation_reason')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevocationReason($reason) {
    $this->set('field_revocation_reason', $reason);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isRevoked() {
    return (bool) $this->get('field_revoked')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevocation($revoked) {
    $this->set('status', $revoked ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Assertion entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Assertion entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Assertion is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

}
