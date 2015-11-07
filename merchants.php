<?php
$merchants_list = array('spawnAlchimiste', 'spwnMineMerchant', 'spwnClayMerchant', 'spwnPetMerchant', 'spwnTurdMerchant',
                        'spwnDyeMerchant', 'spwnBanker', 'spwnBanker2', 'spwnBaker', 'spwnArmurier', 'spwnPoissonier',
                        'spwnPrimeur', 'spwnBoucher', 'spwnMarchevo', 'spwnRaidstone', 'spwnFleuriste', 'spwnDrugStore',
                        'spwnBarman', 'spwnServSalThe', 'spwnTPMerchant', 'spwnPrismarin', 'spwnPancartier');
foreach ($merchants_list as $merchants_list_element) {

  echo 'execute @e[type=Squid,name=' . $merchants_list_element . '] ~ ~ ~ blockdata ~ ~ ~ {Text1:"{\"text\":\" \",\"color\":\"dark_red\",\"bold\":\"true\",\"clickEvent\":{\"action\":\"run_command\",\"value\":\"/scoreboard players set @a ' . $merchants_list_element . ' 1\"}}",Text2:"{\"text\":\"Appel\",\"color\":\"dark_red\",\"bold\":\"true\"}",Text3:"{\"text\":\"Marchand\",\"color\":\"dark_red\",\"bold\":\"true\"}",Text4:"{\"text\":\"\",\"color\":\"red\"}",id:"Sign"}';
}
