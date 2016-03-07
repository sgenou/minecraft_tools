<?php
$output = "/execute @p[score_spwnEnchanter_min=1] ~ ~ ~ summon Villager ~0 ~1 ~0 
{
	Profession:2,
	Attributes:
	[
		{
			Name:generic.movementSpeed,
			Base:0.0
		}
	],
	CustomName:Enchanteur,
	CustomNameVisible:0,
	PersistenceRequired:1,
	Offers:
	{
		Recipes:
		[";

$row = 1;
if (($handle = fopen("Couts enchantements.csv", "r")) !== FALSE) {
    while (($temp_data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$temp_data[0] = str_replace(" ", "_", strtolower($temp_data[0]));
			$data[] = $temp_data;
        }
    
    fclose($handle);
}

$row = 1;
if (($handle = fopen("ench_ids.csv", "r")) !== FALSE) {
    while (($temp_data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$ench_ids[$temp_data[0]] = $temp_data[1];
        }
    
    fclose($handle);
}

foreach ($data as $data_element) {
	$output .= "	{
				maxUses:1000000,
				buy:
				{
					id:diamond,
					Count:" . $data_element[2] . " 
				},
				sell:
				{
					id:enchanted_book,
					Count:1,
					tag:
					{
						StoredEnchantments:
							[
								{
									id:" . $ench_ids[$data_element[0]] . ",
									lvl:" . $data_element[1] . "
								}
							]
					}
				},
				rewardExp: false
			},";
}
$output = substr($output, 0, -1);

$output .= "]
	}
}
";

file_put_contents('marchand_enchantemenets.txt', $output);