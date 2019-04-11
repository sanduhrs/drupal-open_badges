<?php

namespace Drupal\badge\Entity;

use Drupal;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\file\FileInterface;
use Drupal\issuer\Entity\Issuer;
use Drupal\taxonomy\Entity\Term;
use Drupal\user\UserInterface;

/**
 * Defines the Badge entity.
 *
 * @ingroup badge
 *
 * @ContentEntityType(
 *   id = "badge",
 *   label = @Translation("Badge"),
 *   handlers = {
 *     "storage" = "Drupal\badge\BadgeStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\badge\BadgeListBuilder",
 *     "views_data" = "Drupal\badge\Entity\BadgeViewsData",
 *     "translation" = "Drupal\badge\BadgeTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\badge\Form\BadgeForm",
 *       "add" = "Drupal\badge\Form\BadgeForm",
 *       "edit" = "Drupal\badge\Form\BadgeForm",
 *       "delete" = "Drupal\badge\Form\BadgeDeleteForm",
 *     },
 *     "access" = "Drupal\badge\BadgeAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\badge\BadgeHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "badge",
 *   data_table = "badge_field_data",
 *   revision_table = "badge_revision",
 *   revision_data_table = "badge_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer badge entities",
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
 *     "canonical" = "/badge/{badge}",
 *     "add-form" = "/admin/content/badge/add",
 *     "edit-form" = "/admin/content/badge/{badge}/edit",
 *     "delete-form" = "/admin/content/badge/{badge}/delete",
 *     "version-history" = "/admin/content/badge/{badge}/revisions",
 *     "revision" = "/admin/content/badge/{badge}/revisions/{badge_revision}/view",
 *     "revision_revert" = "/admin/content/badge/{badge}/revisions/{badge_revision}/revert",
 *     "revision_delete" = "/admin/content/badge/{badge}/revisions/{badge_revision}/delete",
 *     "translation_revert" = "/admin/content/badge/{badge}/revisions/{badge_revision}/revert/{langcode}",
 *     "collection" = "/admin/content/badge",
 *   },
 *   field_ui_base_route = "badge.settings"
 * )
 */
class Badge extends RevisionableContentEntityBase implements BadgeInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
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

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the badge owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->get('field_description')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->set('field_description', $description);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getImage() {
    /** @var \\Drupal\Core\Field\EntityReferenceFieldItemList $target */
    $target = $this->get('field_image')->first()->getValue();
    $file = Drupal::entityTypeManager()->getStorage('file')->load($target['target_id']);
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
  public function getIssuer() {
    /** @var \\Drupal\Core\Field\EntityReferenceFieldItemList $target */
    $target = $this->get('field_issuer')->first()->getValue();
    return Issuer::load($target['target_id']);
  }

  /**
   * {@inheritdoc}
   */
  public function setIssuer(Issuer $issuer) {
    $this->set('field_issuer', $issuer->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTags() {
    // TODO: Implement getTags() method.
  }

  /**
   * {@inheritdoc}
   */
  public function setTags(Term $term) {
    // TODO: Implement setTags() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getCriteria() {
    // TODO: Implement getCriteria() method.
  }

  /**
   * {@inheritdoc}
   */
  public function setCriteria(array $criteria) {
    // TODO: Implement setCriteria() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getAlignment() {
    // TODO: Implement getAlignment() method.
  }

  /**
   * {@inheritdoc}
   */
  public function setAlignment(array $alignment) {
    // TODO: Implement setAlignment() method.
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
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Badge entity.'))
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
      ->setDescription(t('The name of the Badge entity.'))
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
      ->setDescription(t('A boolean indicating whether the Badge is published.'))
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
