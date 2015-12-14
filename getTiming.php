<?php
	$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";		
	//**********************************************************************************************************************
	// V1.0 : Script qui fournit le temps passé en j/h/mn dans la dernière valeur des états de la liste définie
	//*************************************** API eedomus ******************************************************************
	$api_user = "XXXXXX";
	$api_secret = "yyyyyyyyyyyyyy";
	//*************************************** Tableau des états *******************************************************
        $tabetats = array(1 => "123456", 2 => "123456");
	//**********************************************************************************************************************
	$xml .= "<ETATS>";
	$ietat = 1;
	foreach($tabetats as $periphid) {
		$urlValue =  "https://api.eedomus.com/get?action=periph.caract&periph_id=".$periphid."&api_user=".$api_user."&api_secret=".$api_secret;
		$arrValue = json_decode(utf8_encode(file_get_contents($urlValue)));
		if(array_key_exists("body", $arrValue) && array_key_exists("last_value_change", $arrValue->body)) {
			list($an,$mo,$jo,$he,$mi,$se)=sscanf($arrValue->body->last_value_change,"%d-%d-%d %d:%d:%d");
			$timestamp=mktime($he,$mi,$se,$mo,$jo,$an);
			$difference = time()-$timestamp;
			$jour = floor($difference/86400);
 			$reste1 = ($difference%86400);
 			$heure = floor($reste1/3600);
			$reste2 = ($reste1%3600);
			$minute = floor($reste2/60);
			$xml .= "<ETAT_".$ietat."><TIMING>";
			$timing = "";
			if ($jour > 1) {
				$timing .= $jour." jours, ";
			}
			else if ($jour == 1) {
				$timing .= $jour." jour, ";
			}
			if ($heure > 1) {
				$timing.= $heure." heures, ";
			}
			else if ($heure == 1) {
				$timing .= $heure." heure, ";
			}
			if ($minute > 1) {
				$timing .= $minute." minutes";
			}
			else if ($minute == 1) {
				$timing .= $minute." minute";
			}	
			if ($timing == "") {
			        $timing = "moins d'une minute";
			}
			$xml .= $timing."</TIMING>";
			$xml .= "<MESSAGE>".$arrValue->body->last_value_text." depuis ".$timing."</MESSAGE>";
			$xml .= "</ETAT_".$ietat.">";
		}

		$ietat++;
	}
	$xml .= "</ETATS>";
	header("Content-Type: text/xml");
	echo $xml;
?>