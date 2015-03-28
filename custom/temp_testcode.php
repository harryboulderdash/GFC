<?php

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