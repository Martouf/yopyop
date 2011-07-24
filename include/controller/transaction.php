<?php
/*******************************************************************************************
 * Nom du fichier		: transaction.php
 * Date					: 24 juillet 2011
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@martouf.ch
 * But de ce fichier	: Permet de gérer des transactions.
 *******************************************************************************************
 * 
 *  Une transaction, n'est que l'enregistrement du mouvement de monnaie. Mais aucune monnaie n'est transférée. C'est juste pour vérifier !
 *  Pour véritablement faire le transfert de monnaie, c'est le manager de personne qui s'occupe de débiter et créditer les comptes.
 * 
 * 
 * 
 */

	// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
	$idTransaction = $ressourceId;

	// détermine l'action demandée (add, update, delete, par défaut on suppose que c'est get, donc on ne l'indique pas)
	$action = "get";
	if (isset($parametreUrl['add'])) {
		$action = 'add';
	}
	if (isset($parametreUrl['new'])) {
		$action = 'new';
	}

	// obtient le format de sortie. Si rien n'est défini, on choisi html
	if (empty($ressourceOutput)) {
		$outputFormat = 'html';
	}else{
		$outputFormat = $ressourceOutput;
	}

	// obtient les tags existants et les places dans le tableau $tags ou retourne une chaine vide si aucun tag n'est défini.
	if (empty($ressourceTags)) {
		$tags = "";
	}else{
		$tags = explode("/", trim($ressourceTags,"/")); // transforme la chaine séparée par des / en tableau. Au passage supprime les / surnuméraires en début et fin de chaine
	}

/*
 *  Attention, il faut encore mettre en place la gestion des permissions !!!
 * Pour l'instant on limite l'accès en lecture et en écriture aux utilisateurs connus.
 */

if ($_SESSION['id_personne'] != '1') {
	
	////////////////
	////  ADD
	///////////////
	
	if ($action=='add') {
		// obtient les données
		if(isset($_POST['inputFortune'])){
			$fortune = $_POST['inputFortune'];
		}else{
			$fortune ='';
		}
		if(isset($_POST['inputIdSource'])){
			$id_source = $_POST['inputIdSource'];
		}else{
			$id_source ='';
		}
		if(isset($_POST['inputIdDestinataire'])){
			$id_destinataire = $_POST['inputIdDestinataire'];
		}else{
			$id_destinataire ='';
		}
		if(isset($_POST['inputMontant'])){
			$montant = $_POST['inputMontant'];
		}else{
			$montant ='';
		}
		if(isset($_POST['inputLibelle'])){
			$libelle = $_POST['inputLibelle'];
		}else{
			$libelle ='';
		}
	
		$personneSource = $personneManager->getPersonne($id_source);
		$destinataire = $personneManager->getPersonne($id_destinataire);
		
		// on vérifie que le montant est assez élevé pour faire la transaction !
		$fortuneSource = $personneSource['fortune'];
		
		if ($fortuneSource >= $montant) {  // si le montant est assez élevé ou égal... on transfert la somme.
			$personneManager->augmenteFortune($id_destinataire,$montant);
			$personneManager->diminueFortune($id_source,$montant);
			
			// On crée une transaction pour mémoriser la transaction au cas où une erreur se serait produite.
			// Ensuite on notifie tout les intéressés de la transaction.

			$nom = "Don de ".$personneSource['surnom']." pour ".$destinataire['surnom'];
			$description = $libelle;

			// ajoute la nouvelle ressource
			$idTransaction = $transactionManager->insertTransaction($nom,$description,$id_source,$id_destinataire,$montant);

			$messageTransactionSource = "Vous avez donné ".$montant." kong à <a href=\"http://".$serveur."/profile/".$destinataire['id_personne']."-".$destinataire['surnom'].".html\">".$destinataire['surnom']."</a>";
			$messageTransactionDestinataire = "<a href=\"http://".$serveur."/profile/".$personneSource['id_personne']."-".$personneSource['surnom'].".html\">".$personneSource['surnom']."</a> vous a donné ".$montant." kong";

		// on notifie par email le destinataire
			$messageTransactionMail = "<p>".$messageTransactionDestinataire."</p><p>".$libelle."</p>";
			$envoiOk = $notificationManager->notificationMail($destinataire['email'],$messageTransactionMail,'Notification yopyop.ch');

			// notification aux deux parties
			// destinataire
			$notificationManager->insertNotification('Payement',$messageTransactionDestinataire,'7','0',$destinataire['id_personne']);
			// source
			$notificationManager->insertNotification('Payement',$messageTransactionSource,'7','0',$personneSource['id_personne']);

			// redirige sur l'interface de profil de la personne
			$urlProfile = "http://".$serveur."/profile/".$personneSource['id_personne']."-".$personneSource['surnom'].".html";
			header("Location: ".$urlProfile);
			
		}else{
			echo "Le montant de votre fortune (".$fortuneSource." Kong) n'est pas assez élevée pour donner ".$montant." kong !";
		}
		
	
	////////////////
	////  NEW
	///////////////
	}elseif ($action=='new') {
		
		if(isset($parametreUrl['for'])){
			$id_destinataire = $parametreUrl['for'];
		}else{
			$id_destinataire ='';
		}
		
		if (!empty($id_destinataire)) {
			
			// obtient les infos sur la personne courante. Ainsi on est certain que ce n'est pas une autre personne qui peut payer à sa place.
			$personneCourante = $personneManager->getPersonne($_SESSION['id_personne']);
			$destinataire = $personneManager->getPersonne($id_destinataire);
			
			$smarty->assign('personne',$personneCourante);
			$smarty->assign('destinataire',$destinataire);
			
			// quelques scripts utiles
			$additionalHeader = "
				<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/datePicker.css\" media=\"screen\" />
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>

				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/date.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/date_fr.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.datePicker.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/global.js\"></script>
			";	
			$smarty->assign('additionalHeader',$additionalHeader);

			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("transaction_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
			}else{
				// affiche le formulaire de modification inclu dans le template du thème index.tpl
				$smarty->assign('contenu',"transaction_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
				$smarty->display($theme."index.tpl");
			}
		}else{	
			echo "si le destinataire du don n'est pas fourni je ne sais pas à qui donner des kong moi !!!";
		}

	
		////////////////
		////  GET
		///////////////

	}elseif ($action=='get') {
		
		// uniquement pour les admin
		
		if ($_SESSION['rang'] == '1') {
			// il y a 2 cas possibles qui peuvent être demandés. Une ressource unique bien précise, ou un groupe de ressource.

			// une ressource unique
			if (!empty($idTransaction)) {

				// va chercher les infos sur la ressource demandée
				$transaction = $transactionManager->getTransaction($idTransaction);

				// supprime les \
				stripslashes_deep($transaction);

				// obtient la clé gravatar d'un e-mail, ainsi l'image de profile est le gravatar
				$transaction['gravatar'] = md5($transaction['email']);

				// affichage de la ressource
				$smarty->assign('transaction',$transaction);	

				// quelques scripts utiles
				$additionalHeader = "
					<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/datePicker.css\" media=\"screen\" />
					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>

					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/date.js\"></script>
					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/date_fr.js\"></script>
					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.datePicker.js\"></script>
					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/transaction.js\"></script>";	
				$smarty->assign('additionalHeader',$additionalHeader);

				// certains formats ne sont jamais inclu dans un thème
				if ($outputFormat=='vcf') {
					header('Content-Type: text/x-vcard');
					$smarty->display("transaction_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
				}elseif ($outputFormat=='xml') {

					// calcule le nom de la même ressource, mais en page html
					$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
					$smarty->assign('alternateUrl',"http://".$alternateUrl);

					header('Content-Type: application/atom+xml; charset=UTF-8');
					$smarty->display("transaction_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
				}else{

					// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
					if ($theme=="no") {
						$smarty->display("transaction_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
					}else{
						// affiche la ressource inclue dans le template du thème index.tpl
						$smarty->assign('contenu',"transaction_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
						$smarty->display($theme."index.tpl");
					}
				} // if format = vcf


			// un groupe de ressources
			}else{

				// si aucun tag est passé en paramètre, on affiche la liste complète de toutes les ressources.
				//http://yopyop.ch/transaction/    => va afficher la liste de toutes les transactions.
				if (empty($tags)) {
					$transactions = $transactionManager->getTransactions();
				}else{

					 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
					$taggedElements = $groupeManager->getElementByTags($tags,'transaction');

					$transactions = array(); // tableau contenant des tableaux représentant la ressource
					// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getTransactions()... et array_intersect
					foreach ($taggedElements as $key => $idTransaction) {
						$transactions[$idTransaction] = $transactionManager->getTransaction($idTransaction);
					}
				} // if $tags

				// supprime les \
				stripslashes_deep($transactions);

				// transmets les ressources à smarty
				$smarty->assign('transactions',$transactions);

				// quelques scripts utiles
				$additionalHeader = "
					<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/datePicker.css\" media=\"screen\" />
					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>

					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/date.js\"></script>
					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/date_fr.js\"></script>
					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.datePicker.js\"></script>
					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/transaction.js\"></script>";	
				$smarty->assign('additionalHeader',$additionalHeader);

				if ($outputFormat=='xml') {
					// calcule le nom de la même ressource, mais en page html
					$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
					$smarty->assign('alternateUrl',"http://".$alternateUrl);

					header('Content-Type: application/atom+xml; charset=UTF-8');
					$smarty->display("transaction_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
				}else{

					// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
					if ($theme=="no") {
						$smarty->display("transaction_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags.
					}else{
						// affiche la ressource inclue dans le template du thème index.tpl
						$smarty->assign('contenu',"transaction_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags. On va chercher le template dans la langue demandée et dans le format de sortie demandé.
						$smarty->display($theme."index.tpl");
					}	
				} // if output = xml

			} //if groupe de ressource
		} // admin
	
	} // get

} // if utilisateur connu
?>
