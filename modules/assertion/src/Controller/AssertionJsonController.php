<?php

namespace Drupal\assertion\Controller;

use Drupal\assertion\Entity\Assertion as AssertionEntity;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AssertionJsonController.
 */
class AssertionJsonController extends ControllerBase {

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
   * The route builder.
   *
   * @param \Drupal\assertion\Entity\Assertion $assertion
   *   The assertion object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The json response object.
   */
  public function build(AssertionEntity $assertion) {
    $this->salt = "deadsee";

    $badge = $assertion->getBadge();
    $evidence = $assertion->getEvidence();
    $recipient = $assertion->getRecipient();
    $open_badges_assertion = [
      "@context" => "https://w3id.org/openbadges/v2",
      "type" => "Assertion",
      "id" => Url::fromRoute('entity.assertion.canonical', ['assertion' => $assertion->id(), '_format' => 'json'], ['absolute' => TRUE])->toString(),
      "recipient" => [
        "type" => "email",
        "identity" => $this->hash($recipient->getEmail()),
        "hashed" => TRUE,
        "salt" => $this->salt,
      ],
      "image" => file_create_url($assertion->getImage()->getFileUri()),
      "evidence" => $evidence->getUrl(),
      "issuedOn" => date('c', $assertion->getCreatedTime()),
      "badge" => Url::fromRoute('entity.badge.canonical', ['badge' => $badge->id(), '_format' => 'json'], ['absolute' => TRUE])->toString(),
      "verification" => [
        "type" => "hosted",
      ],
    ];
    return new JsonResponse($open_badges_assertion, 200);
  }

}
