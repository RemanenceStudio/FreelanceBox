<?


// On initialise les variables
$destinataire = "jsaudax@gmail.com";
$objet = "Premier test !";
$message = "C’est un premier test d’envoi d’un email en php.\n" ;
$message .= "Ceci est la forme la plus simple de l’emploi de la fonction mail() \n";

// On envoi l’email
if ( mail($destinataire, $objet, $message) ) { 
	echo "Envoi du mail réussi.";
}
else {
	echo "Echec de l’envoi du mail.";
}
?>