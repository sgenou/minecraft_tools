<?php
$merchants_list = array('spawnAlchimiste' => 'alchimiste', 'spwnMineMerchant' => 'marchand d\articles de mine', 'spwnClayMerchant' => 'marchand d\'argile', 'spwnPetMerchant' => 'marchand d\'animaux', 'spwnTurdMerchant' => 'bousier',
                        'spwnDyeMerchant' => 'teinturier', 'spwnBanker' => 'banquier', 'spwnBanker2' => 'banquier détail', 'spwnBaker' => 'boulanger', 'spwnArmurier' => 'armurier', 'spwnPoissonier' => 'poissonier',
                        'spwnPrimeur' => 'primeur', 'spwnBoucher' => 'boucher', 'spwnMarchevo' => 'marchand de chevaux', 'spwnRaidstone' => 'marchand de redstone', 'spwnFleuriste' => 'fleuriste', 'spwnDrugStore' => 'marchand du drugstore',
                        'spwnBarman' => 'barman', 'spwnServSalThe' => 'serveur de salon de thé', 'spwnTPMerchant' => 'marchand de perles de téléportations', 'spwnPrismarin' => 'marchand d\'articles de la mer', 'spwnPancartier' => 'marchand de pancartes magiques');
foreach ($merchants_list as $merchants_list_element => $merchants_list_realname) {

  //echo 'execute @e[type=Squid,name=' . $merchants_list_element . '] ~ ~ ~ blockdata ~ ~ ~ {Text1:"{\"text\":\" \",\"color\":\"dark_red\",\"bold\":\"true\",\"clickEvent\":{\"action\":\"run_command\",\"value\":\"/scoreboard players set @p ' . $merchants_list_element . ' 1\"}}",Text2:"{\"text\":\"Appel\",\"color\":\"dark_red\",\"bold\":\"true\"}",Text3:"{\"text\":\"Marchand\",\"color\":\"dark_red\",\"bold\":\"true\"}",Text4:"{\"text\":\"\",\"color\":\"red\"}",id:"Sign"}';
  echo "\r\n";
  //echo 'execute @e[type=Squid,name=' . $merchants_list_element . '] ~ ~ ~ tp @e[type=Squid,r=1] ~ -1000 ~';
  echo '{
				maxUses:1000000,
				buy:{id:diamond,Count:1},
				sell:{
					id:spawn_egg,
					Count:1,
					Damage:94,
					tag:
					{
						ench:[{id:34,lvl:1}],
						display:
						{
							Name:' . $merchants_list_element . ',
							Lore:
							[Appel ' . $merchants_list_realname .']}}}		},
';
  echo '{maxUses:1000000,buy:{id:diamond,Count:16},sell:{id:spawn_egg,Count:1,Damage:94,tag:{ench:[{id:34,lvl:1}],display:{Name:' . $merchants_list_element . ',Lore:[Appel ' . $merchants_list_realname . ']}}}},';
  echo "\r\n";  
}
