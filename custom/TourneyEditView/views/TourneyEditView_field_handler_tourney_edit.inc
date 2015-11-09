<?php

/**
 * @file
 *
 * A Views' field handler for editing a node title.
 *
 */
class TourneyEditView_field_handler_tourney_edit extends views_handler_field {

    function construct() {
        parent::construct();
        $this->additional_fields['field_match_team_1_score'] = array(
            'table' => 'field_data_field_match_team_1_score',
            'field' => 'field_match_team_1_score',
        );
        $this->additional_fields['field_match_team_1_score'] = array(
            'table' => 'field_data_field_match_team_1_score',
            'field' => 'field_match_team_1_score',
        );
    }

    function query() {
        $this->ensure_my_table();
        $this->add_additional_fields();
    }

    /**
     * Render the field contents.
     *
     * @param $values
     * @return string
     */
    function render($values) {
        // Render a Views form item placeholder.
        return '<!--form-item-' . $this->options['id'] . '--' . $this->view->row_index . '-->';
    }

    /**
     * Add to and alter the form.
     */
    function views_form(&$form, &$form_state) {
        // Create a container for our replacements
        $form[$this->options['id']] = array(
            '#type' => 'container',
            '#tree' => TRUE,
        );
        // Iterate over the result and add our replacement fields to the form.
        foreach($this->view->result as $row_index => $row) {
            // Add a text field to the form.  This array convention
            // corresponds to the placeholder HTML comment syntax.
            $form[$this->options['id']][$row_index] = array(
                '#type' => 'textfield',
                '#default_value' => $row->{$this->aliases['nid']},//$row->{$this->aliases['title']},
                '#element_validate' => array('TourneyEditView_field_handler_tourney_edit_validate'),
                '#required' => TRUE,
            );
        }

        // Submit to the current page if not a page display.
        if ($this->view->display_handler->plugin_name != 'page') {
            $form['#action'] = current_path();
        }
    }

    /**
     * Form submit method.
     */
    function views_form_submit($form, &$form_state) {
        // Determine which nodes we need to update.
        $updates = array();
        foreach($this->view->result as $row_index => $row) {
            $value = $form_state['values'][$this->options['id']][$row_index];
            if ($row->{$this->aliases['title']} != $value) {
                $updates[$row->{$this->aliases['nid']}] = $value;
            }
        }

        // Grab the nodes we need to update and update them.
        $nodes = node_load_multiple(array_keys($updates));
        foreach($nodes as $nid => $node) {
            $node->title = $updates[$nid];
            node_save($node);
        }

        drupal_set_message(t('Update @num node titles.', array('@num' => sizeof($updates))));
    }

}

/**
 * Validation callback for the title element.
 *
 * @param $element
 * @param $form_state
 */
function TourneyEditView_field_handler_tourney_edit_validate($element, &$form_state) {
    // Only allow titles where the first character is capitalized.
    if (!ctype_upper(substr($element['#value'], 0, 1))) {
        form_error($element, t('All titles must be capitalized.'));
    }
}