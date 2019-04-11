<?php

/**
 * @file
 * Contains assertion.page.inc.
 *
 * Page callback for Assertion entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Assertion templates.
 *
 * Default template: assertion.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_assertion(array &$variables) {
  // Fetch Assertion Entity Object.
  $assertion = $variables['elements']['#assertion'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
