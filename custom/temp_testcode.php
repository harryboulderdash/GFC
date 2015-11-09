<?php

//
$url_components = explode('/', request_uri());
print $url_components[1];


//**************************
//EDIT VIEW HANDLER INC CODE

class TourneyEditView_field_handler_tourney_edit  extends views_handler_field
{
    function construct()
    {
        parent::construct();
        $this->additional_fields = array(
            'field_match_team_1_score' => array(
                'table' => 'field_data_field_match_team_1_score',
                'field' => 'field_match_team_1_score',
            ),
        );
    }

    function render($values)
    {
        // Render a Views form item placeholder.
        // This causes Views to wrap the View in a form.
        // Render a Views form item placeholder.
        return '<!--form-item-' . $this->options['id'] . '--' . $this->view->row_index . '-->';
    }

    function query()
    {
        $this->ensure_my_table();
        $this->add_additional_fields();
    }

    /**
     * Add to and alter the form created by Views.
     */

    function views_form(&$form, &$form_state)
    {
        // Create a container for our replacements
        $form[$this->options['id']] = array(
            '#type' => 'container',
            '#tree' => TRUE,
        );
        // Iterate over the result and add our replacement fields to the form.
        //Could Add Logic to handle rows differently depending on if user is in team, etc.
        foreach ($this->view->result as $row_index => $row) {
            // Add a text field to the form.  This array convention
            // corresponds to the placeholder HTML comment syntax.
            $form[$this->options['id']][$row_index] = array(
                '#type' => 'textfield',
                '#default_value' => $row->{$this->aliases['field_match_team_1_score']},
                '#element_validate' => array('TourneyEditView__field_handler_tourney_edit_validate'),
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
    function views_form_submit($form, &$form_state)
    {
        // Determine which nodes we need to update.
        $updates = array();
        // Iterate over the view result.
        foreach ($this->view->result as $row_index => $row) {
            // Grab the correspondingly submitted form value.
            $value = $form_state['values'][$this->options['id']][$row_index];
            // If the submitted value is different from the original value add it to the
            // array of nodes to update.
            if ($row->{$this->aliases['field_match_team_1_score']} != $value) {
                $updates[$row->{$this->aliases['nid']}] = $value;
            }
        }


        // Grab the nodes we need to update and update them.
        $nodes = node_load_multiple(array_keys($updates));
        foreach ($nodes as $nid => $node) {
            $wrap = GetWrapperByEntityID($node.nid);
            $wrap->field_team_1_score->set($updates[$nid]);
            $wrap->save();
            //$node->title = $updates[$nid];
            //node_save($node);
        }

        drupal_set_message(t('Update @num node scores.', array('@num' => sizeof($updates))));
    }

}


/**
 * Validation callback for the title element.
 *
 * @param $element
 * @param $form_state
 */
function TourneyEditView_field_handler_tourney_edit_validate($element, &$form_state)
{
    // Only allow titles where the first character is capitalized.
    if (!ctype_upper(substr($element['#value'], 0, 1))) {
        //form_error($element, t('All titles must be capitalized.'));
    }

}
//END VIEW EDIT FORM HANDLER CODE
//**************************

$tournamentid = 79;

$c = new ChallongeAPI('XqrMnBPs15MvmX0izddB4zyIHKswRCoaIAyq4cTt');

//load up GFC tourney data
$node = node_load($tournamentid);
$tourneywrapper = entity_metadata_wrapper('node',$node);


$tournament_id = $tournamentid;
$participants = $c->makeCall("tournaments/$tournament_id/participants");
$participants = $c->getParticipants($tournament_id);
//print_r( $c->result );

// retreive param values needed from GFC data

//iterate through team gfc node ids
foreach($tourneywrapper->field_tournament_teams_entered->getIterator() as $delta => $teamid){

	// then load team node and get team name TODO: could this use id and misc value?
	$teamid = $tourneywrapper->field_tournament_name->value();
	$teamnode = node_load($teamid);
	$wrapperteam = entity_metadata_wrapper('node',$teamnode);
		
	//set variable to team name
	$teamname = $wrapperteam->field_team_name->value();
		
	//update GFC teams with IDs
	foreach($participants as $participant){
		if($participant->name == $teamname){
			//if team matches name get id and update
			$wrapperteam->field_team_challonge_id->set($participant->id);
			$wrapperteam->save();
			return;
		}