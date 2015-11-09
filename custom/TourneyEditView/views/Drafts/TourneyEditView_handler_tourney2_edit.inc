<?php

/**
 * @file
 *
 * A Views' field handler for editing a node title.
 *
 */
class TourneyEditView_field_handler_tourney2_edit extends views_handler_field {


    /**
     * Add to and alter the form.
     */

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

        return '<!--form-item-' . $this->options['id'][1] . '--' . $this->view->row_index . '-->';
        //return '<!--form-item-' . $this['Editable'] . '--' . $this->view->row_index . '-->';
    }



}