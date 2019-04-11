<?php

namespace Drupal\badge\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\badge\Entity\BadgeInterface;

/**
 * Class BadgeController.
 *
 *  Returns responses for Badge routes.
 */
class BadgeController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Badge  revision.
   *
   * @param int $badge_revision
   *   The Badge  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($badge_revision) {
    $badge = $this->entityManager()->getStorage('badge')->loadRevision($badge_revision);
    $view_builder = $this->entityManager()->getViewBuilder('badge');

    return $view_builder->view($badge);
  }

  /**
   * Page title callback for a Badge  revision.
   *
   * @param int $badge_revision
   *   The Badge  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($badge_revision) {
    $badge = $this->entityManager()->getStorage('badge')->loadRevision($badge_revision);
    return $this->t('Revision of %title from %date', ['%title' => $badge->label(), '%date' => format_date($badge->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Badge .
   *
   * @param \Drupal\badge\Entity\BadgeInterface $badge
   *   A Badge  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(BadgeInterface $badge) {
    $account = $this->currentUser();
    $langcode = $badge->language()->getId();
    $langname = $badge->language()->getName();
    $languages = $badge->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $badge_storage = $this->entityManager()->getStorage('badge');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $badge->label()]) : $this->t('Revisions for %title', ['%title' => $badge->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all badge revisions") || $account->hasPermission('administer badge entities')));
    $delete_permission = (($account->hasPermission("delete all badge revisions") || $account->hasPermission('administer badge entities')));

    $rows = [];

    $vids = $badge_storage->revisionIds($badge);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\badge\BadgeInterface $revision */
      $revision = $badge_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $badge->getRevisionId()) {
          $link = $this->l($date, new Url('entity.badge.revision', ['badge' => $badge->id(), 'badge_revision' => $vid]));
        }
        else {
          $link = $badge->link($date);
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
                'entity.badge.translation_revert',
                [
                  'badge' => $badge->id(),
                  'badge_revision' => $vid,
                  'langcode' => $langcode,
                ]
              ) :
              Url::fromRoute(
                'entity.badge.revision_revert',
                [
                  'badge' => $badge->id(),
                  'badge_revision' => $vid,
                ]
              ),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.badge.revision_delete', ['badge' => $badge->id(), 'badge_revision' => $vid]),
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

    $build['badge_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
