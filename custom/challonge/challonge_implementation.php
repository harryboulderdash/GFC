<?php

class ChallongeTools{

//Include Challonge API Class File
//include('/opt/bitnami/apps/drupal/htdocs/includes/challonge.class.php');
//include('/opt/bitnami/apps/drupal/htdocs/includes/challonge_implementation.php');
// createGFCTournament($mynodeidparent);

	public function TestFunction(){
		return 'This is a TEST!';
	}

	public function StartTournament($tournamentid){
	
		//check in tournament
		//get team and match info and update GFC
		//start tournament
		
		//set for debug
		$tournamentid = 88;
		
		$c = new ChallongeAPI('XqrMnBPs15MvmX0izddB4zyIHKswRCoaIAyq4cTt');
		
		//load up GFC tourney data
		$node = node_load($tournamentid);
		$tourneywrapper = entity_metadata_wrapper('node',$node);
		
		$challongeid = $tourneywrapper->field_tournament_challonge_id->value();
		
//STEP 1 -- PUBLISH TOURNEY
		// Publish a tournament -- WORKS!
		// http://challonge.com/api/tournaments/publish/:tournament
		$tournament_id = $tournamentid;
		$params = array();
		$tournament = $c->makeCall("tournaments/publish/$challongeid", $params, "post");
		$tournament = $c->publishTournament($challongeid, $params);
		
//STEP 2 -- START TOURNEY
		// Start a tournament -- WORKS!
		// http://challonge.com/api/tournaments/start/:tournament
		$params = array();
		$tournament = $c->makeCall("tournaments/start/$challongeid", $params, "post");
		$tournament = $c->startTournament($challongeid, $params);
		
		
//STEP 3		--UPDATE TEAMS WITH TEAM IDs
		// Get all participants -- WORKS!
		// http://challonge.com/api/tournaments/:tournament/participants
		$participants = $c->makeCall("tournaments/$challongeid/participants");
		$participants = $c->getParticipants($challongeid);
		//print_r( $c->result );
		
		// retreive param values needed from GFC data
		
		//iterate through team gfc node ids
		foreach($tourneywrapper->field_tournament_teams_entered->getIterator() as $delta => $team){
		
			//set variable to team name
			$teamname = $team->field_team_id->value();
			//print $teamname . "<BR>";
			
								//update GFC teams with IDs
								foreach($participants as $participant){
									
  									if(str_replace ( ' ', '',$participant->misc) == str_replace ( ' ', '',$teamname)){
  										//if team matches name get id and update
  										$team->field_team_challonge_id->set((integer)$participant->id);
  										$team->save();
  										print $participant->misc;
  										return;
  									}
									
								}
		}
		

//STEP 4 -- CREATE MATCHES IN GFC
		//Get all Matches -- WORKS! includes ids for team 1 and team 2 which could be used
		// http://challonge.com/api/tournaments/:tournament/matches
		$tournament_id = $tournamentid;
		$params = array();
		$matches = $c->makeCall("tournaments/$tournament_id/matches", $params);
		$matches = $c->getMatches($tournament_id, $params);
		//print_r( $c->result );
		
		foreach($matches as $match){
		
			//INSERT NEW TEAM RECORD
			//create team members entity and wrap it
			$matchentity = entity_create('node', array('type' =>'match'));
			$emw = entity_metadata_wrapper('match',$matchentity);
			
			//set field values
			$emw ->field_match_challonge_id = $match->id;
			$emw ->field_match_team_1 = $match->player1-id;
			$emw ->field_match_team_2 = $match->player2-id;  
			$emw ->field_match_round = $match->round;
			$emw ->title = $tourneywrapper->field_tournament_name->value() . "_" . (string)$match->id;
					
			//save it out
			$emw->save();
			
			//add to tournament
			$tourneywrapper->field_tournament_match[] = $emw->nid;
			$tourneywrapper->save();
		}
		

	}

	public function createGFCTournament($mynodeidparent) {
		
		// /********************************************************************************
		// //CREATE A TOURNAMENT and Update Drupal Tournaments Node with Challonge tourney ID
		//		
				
		// Include the Challonge API Class
		//include('/opt/bitnami/apps/drupal/htdocs/includes/challonge.class.php');
		$c = new ChallongeAPI('XqrMnBPs15MvmX0izddB4zyIHKswRCoaIAyq4cTt');
		
		// get the GFC data needed to create tourney in Challonge
								//$mynodeidparent = 18; //test id for debugging
		$node = node_load($mynodeidparent);
		$wrapper = entity_metadata_wrapper('node',$node);
		
		// retreive param values needed from GFC data
		
		// get tourney name
		$tourney_name = $wrapper->field_tournament_name->value(); 
		
		// get type of play
		$term = taxonomy_term_load ( $wrapper->field_tournament_type->raw()); 
		$wrapper2 = entity_metadata_wrapper('taxonomy_term',$term);
		$tourney_type = $term->name;
		
		// get description
		$tourney_descriptions = $wrapper->field_tournament_description->value (); 
		
		//get url value
		$tourney_url = (string)uniqid(); //str_replace ( ' ', '', $tourney_name ); // TODO: invent good url scheme	                                                  
		                                                    
		// set paramater values for challonge from GFC values
		$params = array (
				"tournament[name]" => $tourney_name,
				"tournament[tournament_type]" => $tourney_type, // see if this can be dynamic
				"tournament[url]" => $tourney_url,
				"tournament[description]" => $tourney_descriptions 
		);
		
		// call to challonge to create tournament
		$tournament = $c->makeCall ("tournaments",$params,"post");
		$tournament = $c->createTournament($params);
		
		//Save new URL value to GFC
		$wrapper->field_tournament_challonge_url->set($tourney_url);
		// save value
		$wrapper->save();
		
		// *************************
		// update GFC with returned tournament ID
		// *************************
		
		//****************NOTE: Need to reload by URL value from CHALLONGE due to bug
		$tournament_id = $tourney_url;
		$params = array("include_matches " => 0);
		$tournament = $c->makeCall("tournaments/$tournament_id", $params, "get");
		$tournament = $c->getTournament($tournament_id, $params);
		
		
		// retreive new tournament id and set to variable
		$challonge_id = (integer)$tournament->id;
		
		// id set value
		$wrapper->field_tournament_challonge_id->set($challonge_id);
		//set url value
		
		// save value
		$wrapper->save();
		
		return true;
	
	}
}


?>