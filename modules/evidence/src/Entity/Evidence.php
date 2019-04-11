<?php

namespace Drupal\evidence\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Evidence entity.
 *
 * @ingroup evidence
 *
 * @ContentEntityType(
 *   id = "evidence",
 *   label = @Translation("Evidence"),
 *   handlers = {
 *     "storage" = "Drupal\evidence\EvidenceStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\evidence\EvidenceListBuilder",
 *     "views_data" = "Drupal\evidence\Entity\EvidenceViewsData",
 *     "translation" = "Drupal\evidence\EvidenceTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\evidence\Form\EvidenceForm",
 *       "add" = "Drupal\evidence\Form\EvidenceForm",
 *       "edit" = "Drupal\evidence\Form\EvidenceForm",
 *       "delete" = "Drupal\evidence\Form\EvidenceDeleteForm",
 *     },
 *     "access" = "Drupal\evidence\EvidenceAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\evidence\EvidenceHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "evidence",
 *   data_table = "evidence_field_data",
 *   revision_table = "evidence_revision",
 *   revision_data_table = "evidence_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer evidence entities",
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
 *     "canonical" = "/evidence/{evidence}",
 *     "add-form" = "/admin/content/evidence/add",
 *     "edit-form" = "/admin/content/evidence/{evidence}/edit",
 *     "delete-form" = "/admin/content/evidence/{evidence}/delete",
 *     "version-history" = "/admin/content/evidence/{evidence}/revisions",
 *     "revision" = "/admin/content/evidence/{evidence}/revisions/{evidence_revision}/view",
 *     "revision_revert" = "/admin/content/evidence/{evidence}/revisions/{evidence_revision}/revert",
 *     "revision_delete" = "/admin/content/evidence/{evidence}/revisions/{evidence_revision}/delete",
 *     "translation_revert" = "/admin/content/evidence/{evidence}/revisions/{evidence_revision}/revert/{langcode}",
 *     "collection" = "/admin/content/evidence",
 *   },
 *   field_ui_base_route = "evidence.settings"
 * )
 */
class Evidence extends RevisionableContentEntityBase implements EvidenceInterface {

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

    // If no revision author has been set explicitly, make the evidence owner
    // the revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
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
  public function getAudience() {
    return $this->get('field_audience')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setAudience($audience) {
    $this->set('field_audience', $audience);
    return $this;
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
  public function getUrl() {
    return $this->get('field_url')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setUrl($uri) {
    $this->set('field_url', $uri);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getGenre() {
    // TODO: Implement getGenre() method.
  }

  /**
   * {@inheritdoc}
   */
  public function setGenre($genre) {
    // TODO: Implement setGenre() method.
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
      ->setDescription(t('The user ID of author of the Evidence entity.'))
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
      ->setDescription(t('The name of the Evidence entity.'))
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
      ->setDescription(t('A boolean indicating whether the Evidence is published.'))
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
