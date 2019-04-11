<?php

/**
 * @file
 * Contains recipient.page.inc.
 *
 * Page callback for Recipient entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Recipient templates.
 *
 * Default template: recipient.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_recipient(array &$variables) {
  // Fetch Recipient Entity Object.
  $recipient = $variables['elements']['#recipient'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
