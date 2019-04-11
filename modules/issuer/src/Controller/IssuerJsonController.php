<?php

namespace Drupal\issuer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\issuer\Entity\Issuer as IssuerEntity;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class IssuerJsonController.
 */
class IssuerJsonController extends ControllerBase {

  /**
   * The route builder.
   *
   * @param \Drupal\issuer\Entity\Issuer $issuer
   *   The issuer object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The json response object.
   */
  public function build(IssuerEntity $issuer) {
    $open_badges_issuer = [
      "@context" => "https://w3id.org/openbadges/v2",
      "type" => "Issuer",
      "id" => Url::fromRoute('entity.issuer.canonical', ['issuer' => $issuer->id(), '_format' => 'json'], ['absolute' => TRUE])->toString(),
      "name" => $issuer->getName(),
      "image" => file_create_url($issuer->getImage()->getFileUri()),
      "url" => $issuer->getUrl(),
      "email" => $issuer->getEmail(),
    ];
    return new JsonResponse($open_badges_issuer, 200);
  }

}
