<?php

namespace Drupal\evidence\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\evidence\Entity\EvidenceInterface;

/**
 * Class EvidenceController.
 *
 *  Returns responses for Evidence routes.
 */
class EvidenceController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Evidence  revision.
   *
   * @param int $evidence_revision
   *   The Evidence  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($evidence_revision) {
    $evidence = $this->entityManager()->getStorage('evidence')->loadRevision($evidence_revision);
    $view_builder = $this->entityManager()->getViewBuilder('evidence');

    return $view_builder->view($evidence);
  }

  /**
   * Page title callback for a Evidence  revision.
   *
   * @param int $evidence_revision
   *   The Evidence  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($evidence_revision) {
    $evidence = $this->entityManager()->getStorage('evidence')->loadRevision($evidence_revision);
    return $this->t('Revision of %title from %date', ['%title' => $evidence->label(), '%date' => format_date($evidence->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Evidence .
   *
   * @param \Drupal\evidence\Entity\EvidenceInterface $evidence
   *   A Evidence  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(EvidenceInterface $evidence) {
    $account = $this->currentUser();
    $langcode = $evidence->language()->getId();
    $langname = $evidence->language()->getName();
    $languages = $evidence->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $evidence_storage = $this->entityManager()->getStorage('evidence');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $evidence->label()]) : $this->t('Revisions for %title', ['%title' => $evidence->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all evidence revisions") || $account->hasPermission('administer evidence entities')));
    $delete_permission = (($account->hasPermission("delete all evidence revisions") || $account->hasPermission('administer evidence entities')));

    $rows = [];

    $vids = $evidence_storage->revisionIds($evidence);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\evidence\EvidenceInterface $revision */
      $revision = $evidence_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $evidence->getRevisionId()) {
          $link = $this->l($date, new Url('entity.evidence.revision', ['evidence' => $evidence->id(), 'evidence_revision' => $vid]));
        }
        else {
          $link = $evidence->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute(
                'entity.evidence.translation_revert',
                [
                  'evidence' => $evidence->id(),
                  'evidence_revision' => $vid,
                  'langcode' => $langcode,
                ]
              ) :
              Url::fromRoute(
                'entity.evidence.revision_revert',
                [
                  'evidence' => $evidence->id(),
                  'evidence_revision' => $vid,
                ]
              ),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.evidence.revision_delete', ['evidence' => $evidence->id(), 'evidence_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['evidence_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
