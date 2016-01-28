<?php
//Plugin ETS - DUNOD - Quiz DCG
//Sébastien GENOU - 7 juillet 2009

/**
 * Le code d'ETS vérifie si cette constante est définie à "true" pour utiliser la fonction " alternativePriceDisplay" pour afficher un prix au lieu de l'affichage de prix standard.
 */
define('ALTERNATIVE_PRICE_DISPLAY', true);

/**
 * Le code d'ETS vérifie si cette constante est définie à "true" pour ne pas afficher le sélecteur de méthode de paiement dans la page de tableau de synthèse financier.
 */
define('NO_BILLING_METHOD_FILTER', true);

/**
 * Le code d'ETS vérifie si cette constante est définie à "true" pour ne pas compter les achats d'UE par Pass Numérique dans le tableau de synthèse financier.
 */
define('dunod_dont_count_pass', true);

/**
 * Le code d'ETS vérifie si cette constante est définie à "true" pour ne pas afficher le texte de description du produit dans la pop-up de confirmation d'ajout de produit au panier. (Obsolète, les popups de confirmation d'ajout au panier ont été supprimées.)
 */
define('NO_PRICE_DESCRIPTION_IN_ADD_CONFIRM_POPUP', true);

/**
 * Le code d'ETS vérifie si cette constante est définie à "true" pour permettre aux utilisateurs de modifier tous les champs de leur fiche de compte (normalement on ne peut modifier que nom et prénom). (Obsolete, une inferface permet maintenant de choisir les champs modifiables)
 */
define('CAN_MODIFY_ALL_FIELDS', true);

/**
 * Le code d'ETS vérifie si cette constante est définie à "true" pour désactiver la synchronisation entre les produits ETS et les sessions ELMG.
 */
define('NO_PRODUCT_SYNC', true);

/**
 * Le code d'ETS vérifie si cette constante est définie à "true" pour ajouter une css pour l'impression.
 */
define('HAS_A_PRINTER_CSS', true);

/**
 * Le code d'ETS vérifie si cette constante est définie à "true" pour afficher les actualités dans l'ordre inverse du standard.
 */
define('NEWS_REVERSE_ORDER', true);

//define('DONT_SHOW_PLATFORM_LINK', true);

/**
 * Affiche le tableau utilisé dans les mails de facture
 *
 * @param array     $products Un tableau qui contient les produits du panier
 * @param int|int[] $type     Le type de produits visés par le tableau ou un tabeau d'id de type(type de validation)
 *
 * @return string
 */
function alternativeGetMailInfoTable($products, $type) {
	if (!is_array($type)) $type = array($type);
	$result = '<span>
	<table style="border: 1px solid #506F86; border-collapse: collapse;">
		<thead style="background: #506F86; font-weight: bold; color: #FFFFFF;">
			<tr>
				<td colspan="2">&nbsp;<a>' . $GLOBALS['LANG']['Nom'] . '</a></td>
				<td colspan="2">&nbsp;<a>' . $GLOBALS['LANG']['Prix'] . '</td>
			</tr>
		</thead>
		<tbody>';
	if (is_array($products)) {
		foreach ($products as $product) {
			if ($type == array(0) || in_array($product->cart_type, $type) || (in_array(2, $type) && $product->cart_type == 5)) {
				$result .= '<tr><td>&nbsp;' . $product->name . '</td><td>&nbsp;</td><td>&nbsp;</td><td>' . (($product->cart_type != 2) ? '-' : number_format(((int)($product->realprice) / 100), 2) . ' &euro;') . '</td></tr>';
			}
		}
	}
	$result .= '
		</tbody>
	</table>
	</span>';
	return $result;
}

/**
 * Filtre la liste des champs de compte utilisateur pour les retourner tous sauf le login
 *
 * @param array $fields_list La liste complete des champs de compte utilisateur
 *
 * @return array
 */
function canModifiyAllFields($fields_list) {
	if (is_array($fields_list)) {
		foreach ($fields_list as $key => $fields_list_element) {
			if ($fields_list_element->custom_field_id == 'userLogin') {
				unset($fields_list[$key]);
			}
		}
	}
	return $fields_list;
}

/**
 * Fonction d'affichage de la page d'accueil
 *
 * @param string  $home_page_title     Titre principal de la page d'accueil
 * @param string  $home_page_text      Texte principal de la page d'accueil
 * @param array   $link                Un tableau contenant les informations sur les boutons intégrés à la page
 * @param string  $home_page_text_next (Inutilisé)
 * @param array   $news                Liste des actualités
 * @param string  $filtered_box_title  (Inutilisé)
 * @param mixed   $field               (Inutilisé)
 * @param mixed   $value               (Inutilisé)
 * @param mixed   $products            (Inutilisé)
 * @param mixed   $perso_fields        (Inutilisé)
 * @param SEOPage $seo                 Objet contenant les informations liées au référencement de la page
 */
function alternativeHomePageDisplay($home_page_title, $home_page_text, $link, $home_page_text_next, $news, $filtered_box_title, $field, $value, $products, $perso_fields, $seo = null) {
	ETS_HTMLPageHome::printHeader($home_page_title, $home_page_text, $link, $seo);
	ETS_HTMLPageHome::printHorizontalButtons();
	ETS_HTMLPageHome::printTitle($home_page_title, 'home_page_title');
	ETS_HTMLPageHome::printLinkAndPictureBox('seedemo');
	ETS_HTMLPageHome::printLinkAndPictureBox('seebanner');
	ETS_HTMLPageHome::printNewsBox($news);
	ETS_HTMLPageHome::printLinkAndPictureBox('seeoffer');
	ETS_HTMLPageHome::printLinkAndPictureBox('registeryourpass');
	ETS_HTMLPageHome::printLinkAndPictureBox('guidedtour');
	ETS_HTMLPageHome::printLinkAndPictureBox('quizondemand');
	ETS_HTMLPageHome::printLinkAndPictureBox('integraloffer');
	ETS_HTMLPageHome::printLinkAndPictureBox('offre_etablissements');
	if (EDIT_MODE) ETS_HTMLPageHome::printSaveAndCancelButtons();
	//ETS_HTMLPageHome::printLinkAndPictureBox('publicite');
	ETS_HTMLPageHome::printFooter();
}

/**
 * Activation du code de Pass Numérique pour un utilisateur
 *
 * @param int    $user_id   L'identifiant de l'utilisateur
 * @param string $card_code Le code du Pass numérique à valider
 *
 * @return bool|int
 */
function pass_validation($user_id, $card_code) {
	if (codeAlreadyRegistered($card_code)) return false;
	$all_codes_list          = file_get_contents(BASEDIR . 'content/CodeList101_EdDunod.txt');
	$has_code_been_validated = strpos($all_codes_list, $card_code);
	$has_code_been_validated = $has_code_been_validated && strlen($card_code) == 9;
	if ($card_code == $GLOBALS['CONF']->getProperty('magic_code') && $GLOBALS['CONF']->getProperty('magic_code')) {
		$has_code_been_validated = true;
	}
	if ($has_code_been_validated) {
		registerPassToUser($user_id, $card_code);
	}
	return $has_code_been_validated;
}

/**
 * Vérifie si l'utilisateur a déjà activé un Pass Numérique
 *
 * @param int $user_id L'identifiant de l'utilisateur
 *
 * @return int
 */
function userAlreadyHasAPass($user_id) {
	$temp_query_result = $GLOBALS['SQL']->Query('SELECT * FROM `pass_codes` WHERE `user_id` = \'' . $user_id . '\'');
	return count($temp_query_result);
}

/**
 * Vérifie si un code a déjà été activé
 *
 * @param string $card_code Le code du Pass numérique à vérifier
 *
 * @return bool|int
 */
function codeAlreadyRegistered($card_code) {
	if ($card_code == $GLOBALS['CONF']->getProperty('magic_code')) return false;
	$temp_query_result = $GLOBALS['SQL']->Query('SELECT * FROM `pass_codes` WHERE `card_code` = \'' . $card_code . '\'');
	return count($temp_query_result);
}

/**
 * Associe un Pass Numérique a un utilisateur
 *
 * @param int    $user_id   L'identifiant de l'utilisateur
 * @param string $card_code Le code du Pass numérique à valider
 *
 * @return bool
 */
function registerPassToUser($user_id, $card_code) {
	$GLOBALS['SQL']->Query('INSERT INTO `pass_codes` (`user_id`, `card_code`, `activation_date`, `credits`) VALUES (\'' . $user_id . '\',\'' . $card_code . '\',\'' . time() . '\',\'' . (int)$GLOBALS['CONF']->getProperty('initial_pass_credits') . '\')');
	$date = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()) + 2);
	setPassEndDate($user_id, $date);
	return true;
}

/**
 * Affiche le prix en fonction de son type
 *
 * @param mixed $price L'objet contenant les informations du prix à afficher.
 *
 * @return string
 */
function alternativePriceDisplay($price) {
	if ($price->type == 4) return '';
	$tempprice = (int)$price->price / 100;
	if ($price->type != 5) $tempprice = number_format($tempprice, 2);
	return (($price->price) ? $tempprice . (($price->type == 5) ? ' ' . $GLOBALS['LANG']['Credits'] : '&nbsp;€') : '');
}

/**
 * Affiche le prix en fonction de son type dans le panier
 *
 * @param mixed $product L'objet contenant les informations du prix à afficher
 *
 * @return string
 */
function alternativeCartPriceDisplay($product) {
	if ($product->type == 4) return '';
	$price = (int)$product->realprice / 100;
	if ($product->cart_type != 5) $price = number_format($price, 2);
	return $price . (($product->cart_type == 5) ? ' ' . $GLOBALS['LANG']['Credits'] : '&nbsp;€');
}

/**
 *
 *
 * @param array     $purchased_products
 * @param bool $simulate
 *
 * @return bool
 */
function pass_card_processing($purchased_products, $simulate = false) {
	$user_id = $GLOBALS['USER']->_properties->user_id;
	if (is_array($purchased_products) && $user_id) {
		foreach ($purchased_products as $purchased_product) {
			if ($purchased_product->cart_type == 5) {
				$total_credits_to_remove += ($purchased_product->realprice / 100) * $purchased_product->quantity;
			}
			$actual_pass_credit = getPassCredit($user_id);
		}
		if (!$simulate) {
			setPassCredit($user_id, $actual_pass_credit - $total_credits_to_remove);
		}
		if ($simulate) return $actual_pass_credit >= $total_credits_to_remove;
	}
}

/**
 * @param int $user_id L'identifiant de l'utilisateur
 *
 * @return int
 */
function getPassCredit($user_id) {
	$tmp_sql_result = $GLOBALS['SQL']->Query('SELECT `credits` FROM `pass_codes` WHERE `user_id` = \'' . $user_id . '\'');
	return (int)$tmp_sql_result[0]->credits;
}

/**
 * @param int $user_id L'identifiant de l'utilisateur
 * @param     $new_credit_value
 *
 * @return mixed
 */
function setPassCredit($user_id, $new_credit_value) {
	if ($new_credit_value >= 0)
		return $GLOBALS['SQL']->Query('UPDATE `pass_codes` SET `credits` = \'' . $new_credit_value . '\' WHERE `user_id` = \'' . $user_id . '\'');
}

/**
 *
 */
function notEnoughCreditsAlert() {
	?>
	<a href="#TB_inline?&height=100&width=450&inlineId=notEnoughCredits&modal=true" id="notEnoughCreditsLink" style="display:none" class="thickbox"></a>
	<!-- Popup pour les sessions pleines -->
	<div id="notEnoughCredits" style="display:none">
		<p class="popup_text">
			<?php echo((userAlreadyHasAPass($GLOBALS['USER']->getproperty('user_id'))) ? $GLOBALS['LANG']['Pas_assez_de_credits'] : $GLOBALS['LANG']['Vous_devez_posseder_un_pass']); ?>
			<br/>
			<br/>
			<input type="button" class="bouton" value="<?php echo $GLOBALS['LANG']['Modifiez_votre_selection']; ?>" onclick="tb_remove();"/>
			<br/>
			<input type="button" class="bouton" value="<?php echo $GLOBALS['LANG']['Activez_votre_pass']; ?>" onclick="<?php echo "window.location='pass_activation.php';" ?>"/>
		</p>
	</div>
	<script type="text/javascript">
		$(function () {
			$("#notEnoughCreditsLink").click();
		});
	</script>
	<?php
}

/**
 * @param int  $user_id L'identifiant de l'utilisateur
 * @param bool $real
 */
function displayRemainingCredits($user_id, $real = false) {
	if (!$real) {
		$cart_content = $GLOBALS['USER']->cart->getProductList();
		if (is_array($cart_content)) {
			foreach ($cart_content as $cart_element) {
				if ($cart_element->cart_type == 5) {
					$count += $cart_element->quantity;
				}
			}
		}
	}
	if (userAlreadyHasAPass($user_id)) {
		echo '<span id="remaining_credits">' . $GLOBALS['LANG']['Credits_restants'] . ': ' . (getPassCredit($user_id) - $count) . '</span>';
	}
}

/**
 * @param int $user_id L'identifiant de l'utilisateur
 */
function displayPassEndDate($user_id) {
	if ($user_id) {
		$tmp_sql = $GLOBALS['SQL']->query('SELECT `activation_date` FROM `pass_codes` WHERE `user_id` = \'' . $user_id . '\'');
		if ($tmp_sql[0]->activation_date) {
			$end_date = strtotime("+2 years", $tmp_sql[0]->activation_date);
			echo '<span id="pass_end_date">' . $GLOBALS['LANG']['Date_de_fin_de_validite_du_pass'] . ': ' . date('d/m/Y', $end_date) . '</span><br />';
		}
	}
}

/**
 *
 */
function displayChallengeYourFriends() {
	?>
	<br/>
	<a href="mailto:?subject=<?php echo $GLOBALS['LANG']['Defiez_sujet']; ?>&body=<?php echo $GLOBALS['LANG']['Defiez_corps']; ?>">
		<?php echo $GLOBALS['LANG']['Defiez_vos_amis']; ?>
	</a>
	<?php
}

/**
 * @param $purchased_products
 */
function extendedAfterPayboxActions($purchased_products) {
	//Enregistrement des infos dans la base elmg pour la gestion des durées de validité
	if (is_array($purchased_products)) {
		foreach ($purchased_products as $purchased_product) {
			if ($purchased_product->action_type == ACTION_TYPE_SESSION_SUBSCRIPTION) {
				if ($purchased_product->cart_type == CART_TYPE_TOKEN) {
					$mode = 'pass';
				} elseif ($purchased_product->cart_type == CART_TYPE_COMMERCIAL) {
					$mode = ((strpos($purchased_product->name, 'offre intégrale') !== false) ? 'integrale' : 'paybox');
				}
				if (isset($mode)) {
					$client = soap_client_singleton::singleton();
					$params = array(
						'user_id' => $GLOBALS['USER']->getProperty('elmg_id'), 'session_id' => $purchased_product->target_id, 'date' => (($mode != 'pass') ? time() : -time()),
						'mode'    => $mode
					);
					$client->call(setDurationInfos, $params);
					unset($mode);
				}
			}
		}
	}
}

/**
 * @param int $user_id L'identifiant de l'utilisateur
 * @param     $date
 */
function setPassEndDate($user_id, $date) {
	$custom_fields_infos = Query::getCustomFieldsListNew('users_custom_fields');
	if (is_array($custom_fields_infos)) {
		foreach ($custom_fields_infos as $custom_fields_info) {
			if ($custom_fields_info->custom_field_name == 'fin_pass') {
				$custom_fields_id = $custom_fields_info->custom_field_id;
			}
		}
	}
	$GLOBALS['SQL']->query('REPLACE INTO `users_custom_fields_content` VALUES (' . $custom_fields_id . ', ' . $user_id . ' , ' . $date . ')');
	$client = soap_client_singleton::singleton();
	$params = array('user_id' => $GLOBALS['USER']->getProperty('elmg_id'), 'date' => $date);
	$client->call(setUserPassEndDate, $params);
}

/**
 * @param $custom_fields_key
 * @param $custom_fields_element
 *
 * @return bool
 */
function extendedUserFieldDisplayProcessing($custom_fields_key, $custom_fields_element) {
	return $custom_fields_element->custom_field_name != 'fin_pass';
}

/**
 *
 */
function printAdditionalFooterScript() {
	if ($_SESSION['PAYBOX_SUCCESFULL'] === true) {
		?>
		<!-- Google Code for Achat en ligne Conversion Page -->
		<script type="text/javascript">
			<!--
			var google_conversion_id = 1070286004;
			var google_conversion_language = "fr";
			var google_conversion_format = "2";
			var google_conversion_color = "ffffff";
			var google_conversion_label = "SLmXCKTipAEQtImt_gM";
			var google_conversion_value = 0;
			//-->
		</script>
		<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
		</script>
		<noscript>
			<div style="display:inline;">
				<img height="1" width="1" style="border-style:none;" alt=""
					 src="http://www.googleadservices.com/pagead/conversion/1070286004/?label=SLmXCKTipAEQtImt_gM&amp;guid=ON&amp;script=0"/>
			</div>
		</noscript>
		<?php
		unset($_SESSION['PAYBOX_SUCCESFULL']);
	}
}

/**
 *
 */
function extendedSyntheseprocessing() {
	$GLOBALS['billing_methods'] = array(CART_TYPE_COMMERCIAL => 'commercial', CART_TYPE_TOKEN => 'pass_card');
}

/**
 * @param $user_infos
 *
 * @return bool
 */
function check_if_not_allready_connected($user_infos) {
	$user_id  = $user_infos[0]->user_id;
	$temp_sql = $GLOBALS['SQL']->Query('SELECT * FROM `sessions` WHERE `user_id` = \'' . $user_id . '\'');
	if ($user_id != 0 && $user_id != 1 && $temp_sql[0]->session_expires > time() && count($temp_sql)) {
		return false;
	}
	return true;
}

$GLOBALS['LANG']['Confirmez_vous_ajout_produit']             = 'Confirmez-vous l\'ajout du produit suivant : ';
$GLOBALS['LANG']['commercial']                               = 'Achat sécurisé en ligne';
$GLOBALS['LANG']['Saisir_un_autre_code']                     = 'Saisir un autre code';
$GLOBALS['LANG']['Credits_restants']                         = 'UE restantes sur votre Pass numérique';
$GLOBALS['LANG']['Vous_devez_avoir_un_compte_pour_activer']  = 'Vous devez posséder un compte pour activer votre Pass numérique';
$GLOBALS['LANG']['Votre_code_a_ete_valide']                  = '<font color="#F35C00">Votre Pass numérique a été correctement activé.<br />Veuillez poursuivre votre inscription en choisissant vos 5 UE de quiz.</font><br />&nbsp;<br /><input type="button" class="bouton" value="Continuer" onclick="window.location=\'catalog.php\'"/>';
$GLOBALS['LANG']['Code_non_valide']                          = '<font color="#F35C00">Le code que vous avez saisi n\'est pas valide !</font>';
$GLOBALS['LANG']['Pass_deja_active']                         = '<font color="#F35C00">Vous avez déjà activé votre Pass numérique !</font><br />Veuillez poursuivre votre inscription en choisissant vos 5 UE.</font><br />&nbsp;<br /><input type="button" class="bouton" value="Continuer" onclick="window.location=\'catalog.php\'"/>';
$GLOBALS['LANG']['pass_card']                                = 'Pass numérique';
$GLOBALS['LANG']['Credits']                                  = 'UE du Pass';
$GLOBALS['LANG']['Pas_assez_de_credits']                     = "Vous avez dépassé le crédit de 5 UE de votre Pass numérique.";
$GLOBALS['LANG']['Modifiez_votre_selection']                 = 'Modifiez votre sélection';
$GLOBALS['LANG']['Abonnement_rss']                           = 'Fil d\'infos RSS';
$GLOBALS['LANG']['Pas_encore_client']                        = 'Je m\'inscris !';
$GLOBALS['LANG']['Action']                                   = 'Mode d\'achat';
$GLOBALS['LANG']['Defiez_vos_amis']                          = 'Défiez vos amis !';
$GLOBALS['LANG']['Defiez_sujet']                             = 'Jouez aux quiz Dunod du DCG et défiez vos amis !';
$GLOBALS['LANG']['Defiez_corps']                             = 'Rejoignez la communaut&eacute; des joueurs des quiz Dunod du DCG sur www.quiz-dcg-dscg.com&nbsp;!%0APlus de 4000 questions &agrave; jouer en ligne pour r&eacute;ussir le DCG, avec conseils, commentaires de correction et tableau de bord personnalis&eacute;.%0AD&eacute;fiez vos amis&nbsp;!';
$GLOBALS['LANG']['Espace_client']                            = 'Déjà joueur ?';
$GLOBALS['LANG']['Nos_formations']                           = 'Liste des UE de quiz proposées';
$GLOBALS['LANG']['Connexion_vers_ma_formation']              = 'Mon espace personnel d\'entraînement';
$GLOBALS['LANG']['Votre_compte_est_active']                  = '<font size="3" color="#003366">Votre compte personnel est désormais activé.<br />Vous allez recevoir un e-mail de confirmation de la création de votre compte.<br />Vos identifiants de connexion restent modifiables à tout moment via la rubrique Mon compte.<br />Vous pouvez désormais <a href="page-32-le-pass-numerique.html">activer votre Pass numérique</a> en sélectionnant vos 5 UE dans le catalogue des quiz ou procéder à l’achat en ligne d’<a href="catalog.php">UE unitaires</a> ou de l’<a href="page-33-l-offre-integrale.html">offre intégrale</a>.</font>';
$GLOBALS['dev_spe_conf_list']                                = array('initial_pass_credits', 'home_page_title', 'home_page_text', 'magic_code');
$GLOBALS['CONFIGURATION']['magic_code']                      = 'Code de Pass passe partout';
$GLOBALS['CONFIGURATION']['initial_pass_credits']            = 'Nombre d\'UE sur un pass';
$GLOBALS['CONFIGURATION']['home_page_title']                 = 'Titre de la page d\'accueil';
$GLOBALS['CONFIGURATION']['home_page_text']                  = 'Description de la page d\'accueil';
$GLOBALS['LANG']['Mon compte']                               = 'Mon compte';
$GLOBALS['LANG']['Vous_etes_enregistre']                     = 'Vous êtes enregistré sur la plate-forme d\'entraînement aux Quiz Dunod du DCG';
$GLOBALS['LANG']['Paiement_enregistre']                      = 'Merci, votre sélection d’une ou de plusieurs UE des quiz Dunod du DCG a bien été ajoutée à votre compte.';
$GLOBALS['LANG']['Numero_personnel_de_votre_pass_numerique'] = '<font size="2" face="arial,helvetica,sans-serif" color="#003366">Numéro personnel de votre Pass numérique</font>';
$GLOBALS['LANG']['Vous_pouvez_vous_connecter']               = 'Vous pouvez dès maintenant vous connecter à votre espace personnel d\'entraînement.';
$GLOBALS['LANG']['Login']                                    = 'Nom d\'utilisateur';
$GLOBALS['LANG']['Vous_avez_deja_achete_ce_produit']         = 'Cette UE fait déjà partie de votre sélection.';
//$GLOBALS['LANG']['Retour_au_catalogue'] = 'Poursuivre ma sélection dans le catalogue';
$GLOBALS['LANG']['Date_de_fin_de_validite_du_pass']   = 'Date de fin de validité commerciale de votre Pass';
$GLOBALS['LANG']['Vous_devez_posseder_un_pass']       = 'Vous devez posséder un Pass numérique.';
$GLOBALS['LANG']['Activez_votre_pass']                = 'Veuillez activer votre Pass Numérique';
$GLOBALS['LANG']['Veuillez_reesayer_ultierieurement'] = 'Veuillez renouveler votre paiement.';
$GLOBALS['LANG']['Paiement_pas_effectue']             = 'Erreur de paiement';
$GLOBALS['LANG']['Vous_etes_deja_enregistre']         = 'Vous êtes déjà enregistré sur la plate-forme des quiz Dunod du DCG';
?>