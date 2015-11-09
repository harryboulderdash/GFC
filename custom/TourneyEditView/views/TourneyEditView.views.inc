<?php

/**
 * @file
 * Views definitions for mymodule module.
 */

/**
 * Implements hook_views_data().
 */
function TourneyEditView_views_data() {
    $data = array();
    $data['node']['score1_handler'] = array(
        'title' => t('Score 1 Handler'),
        'help' => t('Handles custom field for Match Score 1.'),
        'field' => array(
            'handler' => 'TourneyEditView_handler_score1_handler',
        ),
    );
    return $data;
}
