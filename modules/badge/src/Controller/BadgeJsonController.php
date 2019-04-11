<?php

namespace Drupal\badge\Controller;

use Drupal\badge\Entity\Badge;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class BadgeJsonController.
 */
class BadgeJsonController extends ControllerBase {

  /**
   * The route builder.
   *
   * @param \Drupal\badge\Entity\Badge $badge
   *   The badge object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The json response object.
   */
  public function build(Badge $badge) {
    $issuer = $badge->getIssuer();
    $open_badges_badge = [
      "@context" => "https://w3id.org/openbadges/v2",
      "type" => "BadgeClass",
      "id" => Url::fromRoute('entity.badge.canonical', ['badge' => $badge->id(), '_format' => 'json'], ['absolute' => TRUE])->toString(),
      "name" => $badge->getName(),
      "description" => $badge->getDescription(),
      "image" => file_create_url($badge->getImage()->getFileUri()),
      "criteria" => Url::fromRoute('entity.badge.canonical', ['badge' => $badge->id()], ['absolute' => TRUE])->toString(),
      "issuer" => Url::fromRoute('entity.issuer.canonical', ['issuer' => $issuer->id(), '_format' => 'json'], ['absolute' => TRUE])->toString(),
    ];
    return new JsonResponse($open_badges_badge, 200);
  }

}
