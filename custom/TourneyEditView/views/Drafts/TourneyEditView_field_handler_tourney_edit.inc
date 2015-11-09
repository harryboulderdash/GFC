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
        $this->additional_fields['nid'] = 'nid';
       $this->additional_fields['title'] = 'title';
    }

    function query() {
        $this->ensure_my_table();
        $this->add_additional_fields();
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
                    '#maxlength'=> 3,
                    '#size'=> 2,
                    '#default_value' => $this->view->render_field('field_match_team_1_score',$row_index),
                    '#element_validate' => array('view_form_field_handler_title_edit_validate'),
                    '#required' => TRUE,
                );

                $form[$this->options['id']][1][$row_index] = array(
                    '#type' => 'textfield',
                    '#maxlength'=> 3,
                    '#size'=> 2,
                    '#default_value' => $this->view->render_field('field_match_team_2_score',$row_index),
                    '#element_validate' => array('view_form_field_handler_title_edit_validate'),
                    '#required' => TRUE,
                );

            }

        // Submit to the current page if not a page display.
        if ($this->view->display_handler->plugin_name != 'page') {
            $form['#action'] = current_path();
        }
    }

    /**
     * Render the field contents.
     *
     * @param $values
     * @return string
     */
    function render($values) {
        // Render a Views form item placeholder.

        //$values = array();
        //$values[0] = '<!--form-item-' . $this->options['id'] . '99' . '--' . $this->view->row_index . '-->';
        //$values[1] = '<!--form-item-' . $this['Editable'] . '--' . $this->view->row_index . '-->';
        //return $values;

       return '<!--form-item-' . $this->options['id'] . '--' . $this->view->row_index . '-->';
         //return '<!--form-item-' . $this['Editable'] . '--' . $this->view->row_index . '-->';
    }

    /**
     * Form submit method.
     */
    function views_form_submit($form, &$form_state) {
        // Determine which nodes we need to update.
        $updates = array();
        foreach($this->view->result as $row_index => $row) {
            $value = $form_state['values'][$this->options['id']][$row_index];
            $value2 = $form_state['values']['Editable'][$row_index];
            if ($this->view->render_field('field_match_team_1_score',$row_index) != $value or $this->view->render_field('field_match_team_2_score',$row_index) != $value2) {
                $updates[$row->{$this->aliases['nid']}] = $value;
            }
        }

        // Grab the nodes we need to update and update them.
        $nodes = node_load_multiple(array_keys($updates));
        foreach ($nodes as $nid => $node) {
            $wrap = GetWrapperByEntityID($nid);
            $wrap->field_team_1_score->set($value);
            $wrap->field_team_2_score->set($value2);

            $wrap->save();
        }

        drupal_set_message(t('Update @num scores.', array('@num' => sizeof($updates))));
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
        //form_error($element, t('All titles must be capitalized.'));
    }
}