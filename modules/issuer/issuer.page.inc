<?php

/**
 * @file
 * Contains issuer.page.inc.
 *
 * Page callback for Issuer entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Issuer templates.
 *
 * Default template: issuer.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_issuer(array &$variables) {
  // Fetch Issuer Entity Object.
  $issuer = $variables['elements']['#issuer'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
