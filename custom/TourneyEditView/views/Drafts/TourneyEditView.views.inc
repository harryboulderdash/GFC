<?php

/**
 * Implements hook_views_data_alter().
 *
 * @param $data
 *   Information about Views' tables and fields.
 */
function TourneyEditView_views_data_alter(&$data) {
    // Add the Title form field to the node-related fields.
    $data['node']['Score1_Update'] = array(
        'field' => array(
            'title' => t('Team 1 Score'),
            'help' => t('Update Team 1 Score'),
            'handler' => 'TourneyEditView_field_handler_tourney_edit',
        ),
    );

    $data['node']['Score2_Update'] = array(
        'field' => array(
            'title' => t('Team 2 Score'),
            'help' => t('Update Team 2 Score'),
            'handler' => 'TourneyEditView_field_handler_tourney2_edit',
        ),
    );



}


