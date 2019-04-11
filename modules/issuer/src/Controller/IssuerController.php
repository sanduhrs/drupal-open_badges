<?php

namespace Drupal\issuer\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\issuer\Entity\IssuerInterface;

/**
 * Class IssuerController.
 *
 *  Returns responses for Issuer routes.
 */
class IssuerController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Issuer  revision.
   *
   * @param int $issuer_revision
   *   The Issuer  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($issuer_revision) {
    $issuer = $this->entityManager()->getStorage('issuer')->loadRevision($issuer_revision);
    $view_builder = $this->entityManager()->getViewBuilder('issuer');

    return $view_builder->view($issuer);
  }

  /**
   * Page title callback for a Issuer  revision.
   *
   * @param int $issuer_revision
   *   The Issuer  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($issuer_revision) {
    $issuer = $this->entityManager()->getStorage('issuer')->loadRevision($issuer_revision);
    return $this->t('Revision of %title from %date', ['%title' => $issuer->label(), '%date' => format_date($issuer->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Issuer .
   *
   * @param \Drupal\issuer\Entity\IssuerInterface $issuer
   *   A Issuer  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(IssuerInterface $issuer) {
    $account = $this->currentUser();
    $langcode = $issuer->language()->getId();
    $langname = $issuer->language()->getName();
    $languages = $issuer->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $issuer_storage = $this->entityManager()->getStorage('issuer');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $issuer->label()]) : $this->t('Revisions for %title', ['%title' => $issuer->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all issuer revisions") || $account->hasPermission('administer issuer entities')));
    $delete_permission = (($account->hasPermission("delete all issuer revisions") || $account->hasPermission('administer issuer entities')));

    $rows = [];

    $vids = $issuer_storage->revisionIds($issuer);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\issuer\IssuerInterface $revision */
      $revision = $issuer_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $issuer->getRevisionId()) {
          $link = $this->l($date, new Url('entity.issuer.revision', ['issuer' => $issuer->id(), 'issuer_revision' => $vid]));
        }
        else {
          $link = $issuer->link($date);
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
                'entity.issuer.translation_revert',
                [
                  'issuer' => $issuer->id(),
                  'issuer_revision' => $vid,
                  'langcode' => $langcode,
                ]
              ) :
              Url::fromRoute(
                'entity.issuer.revision_revert',
                [
                  'issuer' => $issuer->id(),
                  'issuer_revision' => $vid,
                ]
              ),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.issuer.revision_delete', ['issuer' => $issuer->id(), 'issuer_revision' => $vid]),
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

    $build['issuer_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
