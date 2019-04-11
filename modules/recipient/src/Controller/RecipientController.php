<?php

namespace Drupal\recipient\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\recipient\Entity\RecipientInterface;

/**
 * Class RecipientController.
 *
 *  Returns responses for Recipient routes.
 */
class RecipientController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Recipient  revision.
   *
   * @param int $recipient_revision
   *   The Recipient  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($recipient_revision) {
    $recipient = $this->entityManager()->getStorage('recipient')->loadRevision($recipient_revision);
    $view_builder = $this->entityManager()->getViewBuilder('recipient');

    return $view_builder->view($recipient);
  }

  /**
   * Page title callback for a Recipient  revision.
   *
   * @param int $recipient_revision
   *   The Recipient  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($recipient_revision) {
    $recipient = $this->entityManager()->getStorage('recipient')->loadRevision($recipient_revision);
    return $this->t('Revision of %title from %date', ['%title' => $recipient->label(), '%date' => format_date($recipient->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Recipient .
   *
   * @param \Drupal\recipient\Entity\RecipientInterface $recipient
   *   A Recipient  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(RecipientInterface $recipient) {
    $account = $this->currentUser();
    $langcode = $recipient->language()->getId();
    $langname = $recipient->language()->getName();
    $languages = $recipient->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $recipient_storage = $this->entityManager()->getStorage('recipient');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $recipient->label()]) : $this->t('Revisions for %title', ['%title' => $recipient->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all recipient revisions") || $account->hasPermission('administer recipient entities')));
    $delete_permission = (($account->hasPermission("delete all recipient revisions") || $account->hasPermission('administer recipient entities')));

    $rows = [];

    $vids = $recipient_storage->revisionIds($recipient);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\recipient\RecipientInterface $revision */
      $revision = $recipient_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $recipient->getRevisionId()) {
          $link = $this->l($date, new Url('entity.recipient.revision', ['recipient' => $recipient->id(), 'recipient_revision' => $vid]));
        }
        else {
          $link = $recipient->link($date);
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
                'entity.recipient.translation_revert',
                [
                  'recipient' => $recipient->id(),
                  'recipient_revision' => $vid,
                  'langcode' => $langcode,
                ]
              ) :
              Url::fromRoute(
                'entity.recipient.revision_revert',
                [
                  'recipient' => $recipient->id(),
                  'recipient_revision' => $vid,
                ]
              ),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.recipient.revision_delete', ['recipient' => $recipient->id(), 'recipient_revision' => $vid]),
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

    $build['recipient_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
