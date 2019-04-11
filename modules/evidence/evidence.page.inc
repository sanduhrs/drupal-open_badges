<?php

/**
 * @file
 * Contains evidence.page.inc.
 *
 * Page callback for Evidence entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Evidence templates.
 *
 * Default template: evidence.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_evidence(array &$variables) {
  // Fetch Evidence Entity Object.
  $evidence = $variables['elements']['#evidence'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
