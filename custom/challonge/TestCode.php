<?php
$c = new ChallongeAPI('XqrMnBPs15MvmX0izddB4zyIHKswRCoaIAyq4cTt');
$c->verify_ssl = false;

//LOAD SINGLE TOURNEY
$tournament_id = 551825e232050; //test number
$params = array("include_matches " => 0);
$tournament = $c->makeCall("tournaments/$tournament_id", $params, "get");
$tournament = $c->getTournament($tournament_id, $params);
//print_r($c->result);
//print $tournament->id;

$mynodeidparent = 17;
$node = node_load($mynodeidparent);
$wrapper = entity_metadata_wrapper('node',$node);

$challonge_id = (int)$tournament->id;
$wrapper->field_tournament_challonge_id->set($challonge_id);

// save value
$wrapper->save();

//$tournaments = $c->getTournaments();
//foreach($tournaments->tournament as $tournament) {
//	echo $tournament->tournament-type;
//}

//END GENERAL SAMPLE CODE **************************


// Include the class on your page somewhere
include('/opt/bitnami/apps/drupal/htdocs/includes/challonge.class.php');

// Create a new instance of the API wrapper. Pass your API key into the constructor
// You can view/generate your API key on the 'Password / API Key' tab of your account settings page.
$c = new ChallongeAPI('XqrMnBPs15MvmX0izddB4zyIHKswRCoaIAyq4cTt');

/*
 * For whatever reason (example: developing locally) if you get a SSL validation error from CURL,
 * you can set the verify_ssl attribute of the class to false (defualts to true). It is highly recommended that you
 * *NOT** do this on a production server.
*/
$c->verify_ssl = false;

// TOURNAMENT EXAMPLES
// ************************
// Get all tournaments you created
// http://challonge.com/api/tournaments
$tournaments = $c->makeCall('tournaments');
$tournaments = $c->getTournaments();

print_r($c->result);

//************GET A SINGLE TOURNAMENT BY ID**********************
// Get a tournament
// http://challonge.com/api/tournaments/show/:tournament
$tournament_id = 551825e232050;
$params = array("include_matches " => 0);
$tournament = $c->makeCall("tournaments/$tournament_id", $params, "get");
$tournament = $c->getTournament($tournament_id, $params);

//**************************************************************



//
?>