<?php
//Refonte des ascenseurs avec un item pour remplacer les oeufs de squid
$liste_etages = array(4, 5, 6, 7, 8, 9);
$liste_actions = array('Up' => 'monter', 'Down' => 'descendre');

unlink('ascenceurs_du_marchand.txt');
unlink('blocs_de_commande_ascenceurs.txt');
unlink('initialisation_scoreboard.txt');

foreach ($liste_etages as $etage) {
	foreach ($liste_actions as $action => $traduction) {
		//Contenu des articles du marchand
		file_put_contents('ascenceurs_du_marchand.txt', "{
				maxUses:1000000,
				buy:
				{
					id:diamond,
					Count:1
				},
				sell:
				{
					id:ladder,
					Count:1,
					tag:
					{
						ench:
						[
							{
								id:34s,
								lvl:1s
							}
						],
						display:
						{
							Name:\"" . $action . $etage . "\",
							Lore:
							[
								\"Transforme une pancarte en\",
								\"ascenseur pour " . $traduction . " de " . $etage . " blocs\",
								\"\",
								\"Jeter l'objet au sol sous\",
								\"la pancarte à enchanter.\",
								\"La pancarte doit se trouver\",
								\"deux blocs au dessus du sol.\"
							]
						},
						HideFlags:1
					}
				}
			},
			", FILE_APPEND);
			
			//Blocs de commandes pour le no-mod
			$bloc1 = "scoreboard players set @e[type=Item] " . $action . $etage . " 1 
{
	Item:
	{
		id:\"minecraft:ladder\",
		tag:
		{
			display:
			{
				Name:\"" . $action . $etage . "\"
			},
			ench:
			[
				{
					id:34s,
					lvl:1s
				}
			],
			HideFlags:1
		}
	},
	OnGround:1b
}";
			file_put_contents('blocs_de_commande_ascenceurs.txt', $bloc1 . "\r\n" . $bloc2 . "\r\n" . $bloc3 . "\r\n" ,FILE_APPEND);
			//Initialisation du scoreboard
			$init_command = "/scoreboard objectives add " . $action . $etage . " dummy\r\n";
			file_put_contents('initialisation_scoreboard.txt', $init_command ,FILE_APPEND);
	}
}


