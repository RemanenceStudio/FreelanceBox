<?

///////////////////////////////////////////////////////////////////////////////////
///																				///
///							Configuration du compte								///
///																				///
///////////////////////////////////////////////////////////////////////////////////

			require_once 'Zend/Loader.php';

			Zend_Loader::loadClass('Zend_Gdata');
			Zend_Loader::loadClass('Zend_Gdata_AuthSub');
			Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
			Zend_Loader::loadClass('Zend_Gdata_Calendar');

			$user = 'jsaudax@gmail.com';
			$pass = 'tgp73p76';

			if( isset($_POST['ps']) ) {
				$user = $_POST['ps'];
				$pass = $_POST['pw'];
			}

			$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;

			try {
				$client = Zend_Gdata_ClientLogin::getHttpClient($user,$pass,$service);			
			}
			catch(Exception $e) {

				// prevent Google username and password from being displayed
				// if a problem occurs
				echo "Could not connect to calendar.";
				die();
			}




///////////////////////////////////////////////////////////////////////////////////
///																				///
///							Récupération des events								///
///																				///
///////////////////////////////////////////////////////////////////////////////////


// https://www.google.com/calendar/feeds/jsaudax%40gmail.com/private-95cf30d1b845c74f9f5ea847894cdb25/basic

		// parameters
		$calendar_user = 'jsaudax%40gmail.com';
		$calendar_visibility = 'private-95cf30d1b845c74f9f5ea847894cdb25';		 

		$start_date = '2012-07-25';
		$end_date = '2012-08-26';
		
		if( isset($_POST['ps']) ) {
			$calendar_user = $_POST['cu'];
			$calendar_visibility = $_POST['cv'];
			$start_date = $_POST['dd'];
			$end_date = $_POST['df'];
		}

		
		// If necessary add timezone
		//$start_date = $start_date  . 'T00:00:00.000-07:00';
		//$end_date = $end_date  . 'T00:00:00.000-07:00';		 

		// build query
		$gdataCal = new Zend_Gdata_Calendar($client);
		 
		$query = $gdataCal->newEventQuery();
		$query->setUser($calendar_user);
		$query->setVisibility($calendar_visibility);			
		$query->setSingleEvents(true);
		$query->setProjection('full');
		$query->setOrderby('starttime');
		$query->setSortOrder('ascending');
		$query->setMaxResults(100);
		$query->setStartMin($start_date);
		$query->setStartMax($end_date);

		// execute and get results
		$event_list = $gdataCal->getCalendarEventFeed($query);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Document sans nom</title>
</head>

<body>
<form method="post" action="export.php">
    <table>
        <tr>
            <td>Pseudo google agenda :</td>
            <td><input type="text" name="ps" /></td>
        </tr>
        <tr>
            <td>Passe google agenda :</td>
            <td><input type="password" name="pw" /></td>
        </tr>
        <tr>
            <td>Code user : *</td>
            <td><input type="text" name="cu" /></td>
        </tr>
        <tr>
            <td>Code visibility : *</td>
            <td><input type="text" name="cv" /></td>
        </tr>
        <tr>
            <td>Date d&eacute;part :</td>
            <td><input type="text" name="dd" />
                (YYYY-MM-DD)</td>
        </tr>
        <tr>
            <td>Date fin :</td>
            <td><input type="text" name="df" />
                (YYYY-MM-DD)</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><input type="submit" name="button" id="button" value="Envoyer" /></td>
        </tr>
    </table>
    <p>* Pour ces 2 codes, il faut allez dans les param&egrave;tres de l'agenda et regarder le lien XML priv&eacute;e de l'agenda<br />
        Et copier ces codes comme suit : http://www.google.com/calendar/feeds/<strong>$calendar_user</strong>/<strong>$calendar_visibility</strong>/basic </p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
</form>
<?



///////////////////////////////////////////////////////////////////////////////////
///																				///
///							Afficahge des events								///
///																				///
///////////////////////////////////////////////////////////////////////////////////



		/*echo '<h3>Calendrier de ' .$user. ' / Date début : ' .$start_date. ' - Date fin : ' .$end_date. '</h3>';
		echo '<h4>Nombre de jours trouvés : ' .count($event_list). '</h4>';*/

		//$tabTest = array( 0 => array( 'title' => $event_list[0]->title, 'date' => $start_date) );
		
		/*foreach ($event_list as $event) {
			
			$dateStart[0] = substr($event->when[0]->startTime, 0, 10);*/

			// id
			//print $event->id . '<br />';

			// title
			/*print 'Titre : ' .$event->title . '<br />';

			// where
			print 'Lieu : ' . $event->where[0] . '<br />';

			// description
			print 'Comment : ' . $event->content . '<br />';

			// when (ex: 2010-06-11T07:30:00.000-07:00)
			print $dateStart[0] . ' / ' . $dateStart[1] . '<br />';

			//print $event->when[0]->endTime . '<br />';

		 

			print '-----<br />';*/
			
			
			/*for($j = 0; $j < count($tabTest); $j++) {
				
				echo $tabTest[$j]['date'] .' == '. $dateStart[0] .'<br />';
				
				if($tabTest[$j]['date'] == $dateStart[0]) {
					$tabTest[$i]['title'] .= ' / ' . $event->title;
					//$tabTest[$i]['date'] = $dateStart[0];
					echo 'exite deja<br>';
				}
				else {
					$i++;
					$tabTest[$i]['title'] = $event->title;
					$tabTest[$i]['date'] = $dateStart[0];
					echo 'a ete cree<br>';
				}
			
			}*/
			
			/*$total = count($tabTest) - 1;
			$index = count($tabTest);
			
			if ($dateStart[0] != $tabTest[$total]['date']) {
				echo 'ok<br>';
				$tabTest[$index]['title'] = $event->title;
				$tabTest[$index]['date'] = $dateStart[0];
			}
			else {
				$tabTest[$total]['title'] .= ' / ' . $event->title;
			}*/
			
			
			
			//echo '///////////// count : ' .count($tabTest). ' /////////////////////////////<br><br>';

		/*}
		
		echo '<pre>';
		print_r($tabTest);*/

		/*foreach ($tabTest as $event) {


			// title
			print 'Titre : ' .$event->title . '<br />';

			// when (ex: 2010-06-11T07:30:00.000-07:00)
			print $event['date'] . '<br />';		 

			print '-----<br />';
			
		}*/
?>
</body>
</html>