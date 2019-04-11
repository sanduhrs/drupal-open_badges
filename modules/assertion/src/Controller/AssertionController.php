<?php

namespace Drupal\assertion\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\assertion\Entity\AssertionInterface;

/**
 * Class AssertionController.
 *
 *  Returns responses for Assertion routes.
 */
class AssertionController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Assertion  revision.
   *
   * @param int $assertion_revision
   *   The Assertion  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($assertion_revision) {
    $assertion = $this->entityManager()->getStorage('assertion')
      ->loadRevision($assertion_revision);
    $view_builder = $this->entityManager()->getViewBuilder('assertion');

    return $view_builder->view($assertion);
  }

  /**
   * Page title callback for a Assertion  revision.
   *
   * @param int $assertion_revision
   *   The Assertion  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($assertion_revision) {
    $assertion = $this->entityManager()->getStorage('assertion')
      ->loadRevision($assertion_revision);
    return $this->t('Revision of %title from %date', ['%title' => $assertion->label(), '%date' => format_date($assertion->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Assertion .
   *
   * @param \Drupal\assertion\Entity\AssertionInterface $assertion
   *   A Assertion  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(AssertionInterface $assertion) {
    $account = $this->currentUser();
    $langcode = $assertion->language()->getId();
    $langname = $assertion->language()->getName();
    $languages = $assertion->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $assertion_storage = $this->entityManager()->getStorage('assertion');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $assertion->label()]) : $this->t('Revisions for %title', ['%title' => $assertion->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all assertion revisions") || $account->hasPermission('administer assertion entities')));
    $delete_permission = (($account->hasPermission("delete all assertion revisions") || $account->hasPermission('administer assertion entities')));

    $rows = [];

    $vids = $assertion_storage->revisionIds($assertion);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\assertion\AssertionInterface $revision */
      $revision = $assertion_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $assertion->getRevisionId()) {
          $link = $this->l($date, new Url('entity.assertion.revision', ['assertion' => $assertion->id(), 'assertion_revision' => $vid]));
        }
        else {
          $link = $assertion->link($date);
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
                'entity.assertion.translation_revert',
                [
                  'assertion' => $assertion->id(),
                  'assertion_revision' => $vid,
                  'langcode' => $langcode,
                ]
              ) :
              Url::fromRoute(
                'entity.assertion.revision_revert',
                [
                  'assertion' => $assertion->id(),
                  'assertion_revision' => $vid,
                ]
              ),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute(
                'entity.assertion.revision_delete',
                ['assertion' => $assertion->id(), 'assertion_revision' => $vid]
              ),
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

    $build['assertion_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
