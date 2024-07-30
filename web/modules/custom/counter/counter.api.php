<?php

/**
 * @file
 * Hooks specific to the Hooks Example module.
 */

/**
 *  Display the number of times the node has been viewed
 *
 * This hooks allows modules to display whenever the total number of times the
 * current user has viewed a specific node during their current session is
 * increased.
 *
 * @param int $current_count
 *   The number of times that the current user has viewed the node during this
 *   session.
 */
function hook_display_count(int $current_count) {
  //Implementation to be done by other modules.
}
