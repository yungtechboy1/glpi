<?php
/*
 
  ----------------------------------------------------------------------
GLPI - Gestionnaire libre de parc informatique
 Copyright (C) 2002 by the INDEPNET Development Team.
 Bazile Lebeau, baaz@indepnet.net - Jean-Mathieu Dol�ans, jmd@indepnet.net
 http://indepnet.net/   http://glpi.indepnet.org
 ----------------------------------------------------------------------
 Based on:
IRMA, Information Resource-Management and Administration
Christian Bauer, turin@incubus.de 

 ----------------------------------------------------------------------
 LICENSE

This file is part of GLPI.

    GLPI is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    GLPI is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GLPI; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 ----------------------------------------------------------------------
 Original Author of file:
 Purpose of file:
 ----------------------------------------------------------------------
*/
 

include ("_relpos.php");


function titleSoftware(){

         GLOBAL  $lang,$HTMLRel;
         
         echo "<div align='center'><table border='0'><tr><td>";
         echo "<img src=\"".$HTMLRel."pics/logiciels.png\" alt='".$lang["software"][0]."' title='".$lang["software"][0]."'></td><td><a  class='icon_consol' href=\"software-info-form.php\"><b>".$lang["software"][0]."</b></a>";
         echo "</td></tr></table></div>";
}


function searchFormSoftware() {
	// Print Search Form
	
	GLOBAL $cfg_install, $cfg_layout, $layout, $lang;

	$option["ID"]				= $lang["software"][1];
	$option["name"]				= $lang["software"][2];
	$option["platform"]			= $lang["software"][3];
	$option["location"]			= $lang["software"][4];
	$option["version"]			= $lang["software"][5];
	$option["comments"]			= $lang["software"][6];
/*
	echo "<form method=get action=\"".$cfg_install["root"]."/software/software-search.php\">";
	echo "<center><table border='0' width='90%'>";
	echo "<tr><th colspan='2'><b>".$lang["search"][5].":</b></th></tr>";
	echo "<tr class='tab_bg_1'>";
	echo "<td align='center'>";
		dropdown( "dropdown_locations",  "contains");
	echo "<input type='hidden' name=field value=location>&nbsp;";
	echo $lang["search"][6];
	echo "&nbsp;<select name=sort size=1>";
	reset($option);
	foreach ($option as $key => $val) {
		echo "<option value=$key>$val\n";
	}
	echo "</select>";
	echo "<input type='hidden' name=phrasetype value=exact>";
	echo "</td><td width='80' align='center' class='tab_bg_2'>";
	echo "<input type='submit' value=\"".$lang["buttons"][1]."\" class='submit'>";
	echo "</td></tr></table></form></center>";
 */
	echo "<form method=get action=\"".$cfg_install["root"]."/software/software-search.php\">";
	echo "<center><table class='tab_cadre' width='750'>";
	echo "<tr><th colspan='2'><b>".$lang["search"][0].":</b></th></tr>";
	echo "<tr class='tab_bg_1'>";
	echo "<td align='center'>";
	echo "<select name=\"field\" size=1>";
	reset($option);
	foreach ($option as $key => $val) {
		echo "<option value=$key>$val\n";
	}
	echo "</select>&nbsp;";
	echo $lang["search"][1];
	echo "&nbsp;<select name='phrasetype' size='1'>";
	echo "<option value=contains>".$lang["search"][2]."</option>";
	echo "<option value=exact>".$lang["search"][3]."</option>";
	echo "</select>";
	echo "<input type='text' size='12' name=\"contains\">";
	echo "&nbsp;";
	echo $lang["search"][4];
	echo "&nbsp;<select name='sort' size='1'>";
	reset($option);
	foreach ($option as $key => $val) {
		echo "<option value=$key>$val\n";
	}
	echo "</select> ";
	echo "</td><td width='80' align='center' class='tab_bg_2'>";
	echo "<input type='submit' value=\"".$lang["buttons"][0]."\" class='submit'>";
	echo "</td></tr></table></center></form>";
}

function showSoftwareList($target,$username,$field,$phrasetype,$contains,$sort,$order,$start) {

	// Lists Software

	GLOBAL $cfg_install, $cfg_layout, $cfg_features, $lang;

	// Build query
	if ($phrasetype == "contains") {
		$where = "($field LIKE '%".$contains."%')";
	} else {
		$where = "($field LIKE '".$contains."')";
	}
	if (!$start) {
		$start = 0;
	}
	if (!$order) {
		$order = "ASC";
	}
	
	$query = "SELECT * FROM glpi_software WHERE $where ORDER BY $sort";

	// Get it from database	
	$db = new DB;
	if ($result = $db->query($query)) {
		$numrows = $db->numrows($result);

		// Limit the result, if no limit applies, use prior result
		if ($numrows>$cfg_features["list_limit"]) {
			$query_limit = "SELECT * FROM glpi_software WHERE $where ORDER BY $sort $order LIMIT $start,".$cfg_features["list_limit"]." ";
			$result_limit = $db->query($query_limit);
			$numrows_limit = $db->numrows($result_limit);
		} else {
			$numrows_limit = $numrows;
			$result_limit = $result;
		}

		if ($numrows_limit>0) {
			// Produce headline
			echo "<center><table class='tab_cadre'><tr>";

			// Name
			echo "<th>";
			if ($sort=="name") {
				echo "&middot;&nbsp;";
			}
			echo "<a href=\"$target?field=$field&phrasetype=$phrasetype&contains=$contains&sort=name&order=ASC&start=$start\">";
			echo $lang["software"][2]."</a></th>";

			// Version			
			echo "<th>";
			if ($sort=="version") {
				echo "&middot;&nbsp;";
			}
			echo "<a href=\"$target?field=$field&phrasetype=$phrasetype&contains=$contains&sort=version&order=ASC&start=$start\">";
			echo $lang["software"][5]."</a></th>";

			// Platform		
			echo "<th>";
			if ($sort=="platform") {
				echo "&middot;&nbsp;";
			}
			echo "<a href=\"$target?field=$field&phrasetype=$phrasetype&contains=$contains&sort=platform&order=DESC&start=$start\">";
			echo $lang["software"][3]."</a></th>";

			// Licenses
			echo "<th>".$lang["software"][11]."</th>";
		
			echo "</tr>";

			for ($i=0; $i < $numrows_limit; $i++) {
				$ID = $db->result($result_limit, $i, "ID");

				$sw = new Software;
				$sw->getfromDB($ID);

				echo "<tr class='tab_bg_2'>";
				echo "<td><b>";
				echo "<a href=\"".$cfg_install["root"]."/software/software-info-form.php?ID=$ID\">";
				echo $sw->fields["name"]." (".$sw->fields["ID"].")";
				echo "</a></b></td>";
				echo "<td>".$sw->fields["version"]."</td>";
				echo "<td>".$sw->fields["platform"]."</td>";
				echo "<td>";
					countInstallations($sw->fields["ID"]);
				echo "</td>";
				echo "</tr>";
			}

			// Close Table
			echo "</table></center>";

			// Pager
			$parameters="field=$field&phrasetype=$phrasetype&contains=$contains&sort=$sort";
			printPager($start,$numrows,$target,$parameters);

		} else {
			echo "<center><b>".$lang["software"][22]."</b></center>";
			echo "<hr noshade>";
			searchFormSoftware();
		}
	}
}



function showSoftwareForm ($target,$ID) {
	// Show Software or blank form
	
	GLOBAL $cfg_layout,$cfg_install,$lang;

	$sw = new Software;

	echo "<center><form method='post' action=\"$target\">";
	echo "<table class='tab_cadre'>";
	echo "<tr><th colspan='2'><b>";
	if (!$ID) {
		echo $lang["software"][0].":";
		$sw->getEmpty();
	} else {
		$sw->getfromDB($ID);
		echo $lang["software"][10]." ID $ID:";
	}		
	echo "</b></th></tr>";

	echo "<tr class='tab_bg_1'><td>".$lang["software"][2].":		</td>";
	echo "<td><input type='text' name='name' value=\"".$sw->fields["name"]."\" size='25'></td>";
	echo "</tr>";

	echo "<tr class='tab_bg_1'><td>".$lang["software"][4].": 	</td><td>";
		dropdownValue("glpi_dropdown_locations", "location", $sw->fields["location"]);
	echo "</td></tr>";

	
	echo "<tr class='tab_bg_1'><td>".$lang["software"][3].": 	</td><td>";
		dropdownValue("glpi_dropdown_os", "platform", $sw->fields["platform"]);
	echo "</td></tr>";

	echo "<tr class='tab_bg_1'><td>".$lang["software"][5].":		</td>";
	echo "<td><input type='text' name='version' value=\"".$sw->fields["version"]."\" size='5'></td>";
	echo "</tr>";

	echo "<tr class='tab_bg_1'><td valign='top'>";
	echo $lang["software"][6].":	</td>";
	echo "<td align='center'><textarea cols=35 rows=4 name=comments >".$sw->fields["comments"]."</textarea>";
	echo "</td></tr>";
	
	if (!$ID) {

		echo "<tr>";
		echo "<td class='tab_bg_2' valign='top' colspan='2'>";
		echo "<center><input type='submit' name='add' value=\"".$lang["buttons"][8]."\" class='submit'></center>";
		echo "</td>";
		echo "</form></tr>";

		echo "</table></center>";

	} else {

		echo "<tr>";
		echo "<td class='tab_bg_2' valign='top'>";
		echo "<input type='hidden' name='ID' value=\"$ID\">\n";
		echo "<center><input type='submit' name='update' value=\"".$lang["buttons"][7]."\" class='submit'></center>";
		echo "</td></form>\n\n";
		echo "<form action=\"$target\" method='post'>\n";
		echo "<td class='tab_bg_2' valign='top'>\n";
		echo "<input type='hidden' name='ID' value=\"$ID\">\n";
		echo "<center><input type='submit' name='delete' value=\"".$lang["buttons"][6]."\" class='submit'></center>";
		echo "</td>";
		echo "</form></tr>";

		echo "</table></center>";
		
		showLicenses($ID);
		showLicensesAdd($ID);
		
	}

}

function updateSoftware($input) {
	// Update Software in the database

	$sw = new Software;
	$sw->getFromDB($input["ID"]);
 
 	// Pop off the last attribute, no longer needed
	$null=array_pop($input);
	
	// Fill the update-array with changes
	$x=0;
	foreach ($input as $key => $val) {
		if (empty($sw->fields[$key]) || $sw->fields[$key] != $input[$key]) {
			$sw->fields[$key] = $input[$key];
			$updates[$x] = $key;
			$x++;
		}
	}
	if(!empty($updates)) {
	
		$sw->updateInDB($updates);
	}
}

function addSoftware($input) {
	// Add Software, nasty hack until we get PHP4-array-functions

	$sw = new Software;

	// dump status
	$null = array_pop($input);
	
	// fill array for update
	foreach ($input as $key => $val) {
		if (empty($sw->fields[$key]) || $sw->fields[$key] != $input[$key]) {
			$sw->fields[$key] = $input[$key];
		}
	}

	if ($sw->addToDB()) {
		return true;
	} else {
		return false;
	}
}


function deleteSoftware($input) {
	// Delete Software
	
	$sw = new Software;
	$sw->deleteFromDB($input["ID"]);
	
} 

function dropdownSoftware() {
	$db = new DB;
	$query = "SELECT * FROM glpi_software";
	$result = $db->query($query);
	$number = $db->numrows($result);

	$i = 0;
	echo "<select name=sID size=1>";
	while ($i < $number) {
		$version = $db->result($result, $i, "version");
		$name = $db->result($result, $i, "name");
		$sID = $db->result($result, $i, "ID");
		echo  "<option value=$sID>$name (v. $version)</option>";
		$i++;
	}
	echo "</select>";
}


function showLicensesAdd($ID) {
	
	GLOBAL $cfg_layout,$cfg_install,$lang;
	
	echo "<center><table class='tab_cadre' width='50%' cellpadding='2'>";
	echo "<tr><td align='center' class='tab_bg_2'><b>";
	echo "<a href=\"".$cfg_install["root"]."/software/software-licenses.php?addform=addform&ID=$ID\">";
	echo $lang["software"][12];
	echo "</a></b></td></tr>";
	echo "</table></center><br>";
}

function showLicenses ($sID) {

	GLOBAL $cfg_layout,$cfg_install, $lang;
	
	$db = new DB;

	$query = "SELECT ID FROM glpi_licenses WHERE (sID = $sID)";
	if ($result = $db->query($query)) {
		if ($db->numrows($result)!=0) { 
			echo "<br><center><table cellpadding='2' class='tab_cadre' width='50%'>";
			echo "<tr><th colspan='2'>";
			echo $db->numrows($result);
			echo " ".$lang["software"][13]." :</th>";
			echo "<th colspan='1'>";
			echo " ".$lang["software"][19]." :</th></tr>";
			$i=0;
			while ($data=$db->fetch_row($result)) {
				$ID = current($data);
				$lic = new License;
				$lic->getfromDB($ID);
				echo "<tr class='tab_bg_1'>";
				echo "<td align='center'><b>".$lic->serial."</b></td>";
				echo "<td align='center'><b>";
				echo "<a href=\"".$cfg_install["root"]."/software/software-licenses.php?delete=delete&ID=$ID\">";
				echo $lang["buttons"][6];
				echo "</a></b></td>";
				echo "<td align='center'>";
				$query2="SELECT glpi_inst_software.ID AS ID, glpi_computers.ID AS cID, glpi_computers.name AS cname FROM glpi_inst_software, glpi_computers";
				$query2.= " WHERE glpi_inst_software.cID= glpi_computers.ID AND glpi_inst_software.license=$ID";
				if ($result2 = $db->query($query2)) {
				if ($db->numrows($result2)!=0) { 
				echo "<table width='100%'>";
				while ($data2=$db->fetch_array($result2)) {
					
					echo "<tr><td align=center>";
					echo "<b><a href=\"".$cfg_install["root"]."/computers/computers-info-form.php?ID=".$data2["cID"]."\">";
					echo $data2["cname"];
					echo "</a></b></td><td align=center>";
					echo "<b><a href=\"".$cfg_install["root"]."/software/software-licenses.php?uninstall=uninstall&ID=".$data2["ID"]."\">";
					echo $lang["buttons"][5];
					echo "</a></b>";
					echo "</td></tr>";
					}
					echo "</table>";
				} else { echo "&nbsp;";}
				}
				
				
				echo "</td>";
				
				echo "</tr>";
			}	
			echo "</table></center>\n\n";
		} else {

			echo "<br><center><table border='0' width=50% cellpadding='2'>";
			echo "<tr><th>".$lang["software"][14]."</th></tr>";
			echo "</table></center>";
		}
	}
}


function showLicenseForm($target,$ID) {

	GLOBAL $cfg_install, $cfg_layout, $lang;

	echo "<div align='center'><b>";
	echo "<a href=\"".$cfg_install["root"]."/software/software-info-form.php?ID=$ID\">";
	echo $lang["buttons"][13]."</b>";
	echo "</a><br>";
	
	echo "<table class='tab_cadre'><tr><th colspan='2'>".$lang["software"][15]." ($ID):</th></tr>";
	echo "<form method='post' action=\"$target\">";

	echo "<tr class='tab_bg_1'><td>".$lang["software"][16].":</td>";
	echo "<td><input type='text' size='20' name='serial' value=\"\">";
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'>";
	echo "<td align='center' colspan='2'>";
	echo "<input type='hidden' name='sID' value=".$ID.">";
	echo "<input type='submit' name='add' value=\"".$lang["buttons"][8]."\" class='submit'>";
	echo "</td></form>";

	echo "</table></div>";
}


function addLicense($input) {
	// Add License, nasty hack until we get PHP4-array-functions

	$lic = new License;

	$lic->sID = $input["sID"];
	$lic->serial = $input["serial"];

	if ($lic->addToDB()) {
		return true;
	} else {
		return false;
	}
}

function deleteLicense($ID) {
	// Delete License
	
	$lic = new License;
	$lic->deleteFromDB($ID);
	
} 

function showLicenseSelect($back,$target,$cID,$sID) {

	GLOBAL $cfg_layout,$cfg_install, $lang;
	
	$db = new DB;

	$back = urlencode($back);
	
	$query = "SELECT ID FROM glpi_licenses WHERE (sID = $sID)";
	if ($result = $db->query($query)) {
		if ($db->numrows($result)!=0) { 
			echo "<br><center><table cellpadding='2' class='tab_cadre' width='50%'>";
			echo "<tr><th colspan='3'>";
			echo $db->numrows($result);
			echo " ".$lang["software"][13].":</th></tr>";
			$i=0;
			while ($data=$db->fetch_row($result)) {
				$ID = current($data);
				
				$lic = new License;
				$lic->getfromDB($ID);
				if ($lic->serial!="free") {
				
					$query2 = "SELECT license FROM glpi_inst_software WHERE (license = '$ID')";
					$result2 = $db->query($query2);
					if ($db->numrows($result2)==0) {				
						$lic = new License;
						$lic->getfromDB($ID);
						echo "<tr class='tab_bg_1'>";
						echo "<td><b>$i</b></td>";
						echo "<td width='100%' align='center'><b>".$lic->serial."</b></td>";
						echo "<td align='center'><b>";
						echo "<a href=\"".$cfg_install["root"]."/software/software-licenses.php?back=$back&install=install&cID=$cID&lID=$ID\">";
						echo $lang["buttons"][4];
						echo "</a></b></td>";
						echo "</tr>";
					} else {
						echo "<tr class='tab_bg_1'>";
						echo "<td><b>$i</b></td>";
						echo "<td colspan='2' align='center'>";
						echo "<b>".$lang["software"][18]."</b>";
						echo "</td>";
						echo "</tr>";
					}
					$i++;
				} else {
					echo "<tr class='tab_bg_1'>";
					echo "<td><b>$i</b></td>";
					echo "<td width='100%' align='center'><b>".$lic->serial."</b></td>";
					echo "<td align='center'><b>";
					echo "<a href=\"".$cfg_install["root"]."/software/software-licenses.php?back=$back&install=install&cID=$cID&lID=$ID\">";
					echo $lang["buttons"][4];
					echo "</a></b></td>";
					echo "</tr>";	
				}
			}	
			echo "</table></center><br>\n\n";
		} else {

			echo "<br><center><table border='0' width='50%' cellpadding='2'>";
			echo "<tr><th>".$lang["software"][14]."</th></tr>";
			echo "</table></center><br>";
		}
	}
}

function installSoftware($cID,$lID) {

	$db = new DB;
	$query = "INSERT INTO glpi_inst_software VALUES (NULL,$cID,$lID)";
	if ($result = $db->query($query)) {
		return true;
	} else {
		return false;
	}
}

function uninstallSoftware($ID) {

	$db = new DB;
	$query = "DELETE FROM glpi_inst_software WHERE(ID = '$ID')";
//	echo $query;
	if ($result = $db->query($query)) {
		return true;
	} else {
		return false;
	}
}

function showSoftwareInstalled($instID) {

	GLOBAL $cfg_layout,$cfg_install, $lang;

        $db = new DB;
	$query = "SELECT * FROM glpi_inst_software WHERE (cID = $instID)";
	$result = $db->query($query);
	$number = $db->numrows($result);
	$i = 0;
		
        echo "<form method='post' action=\"".$cfg_install["root"]."/software/software-licenses.php\">";

	echo "<br><br><center><table class='tab_cadre' width='90%'>";
	echo "<tr><th colspan='2'>".$lang["software"][17].":</th></tr>";
	
	while ($i < $number) {
		$lID = $db->result($result, $i, "license");
		$ID = $db->result($result, $i, "ID");
		$query2 = "SELECT sID,serial FROM glpi_licenses WHERE (ID = '$lID')";
		$result2 = $db->query($query2);
		$sID = $db->result($result2,0,"sID");
		$serial = $db->result($result2,0,"serial");
		$sw = new Software;
		$sw->getFromDB($sID);

		echo "<tr class='tab_bg_1'>";
	
		echo "<td align='center'><b><a href=\"".$cfg_install["root"]."/software/software-info-form.php?ID=$sID\">";
		echo $sw->fields["name"]." (v. ".$sw->fields["version"].")</a>";
		echo "</b>";
		echo " - ".$serial."</td>";
		
		echo "<td align='center' class='tab_bg_2'>";
		echo "<a href=\"".$cfg_install["root"]."/software/software-licenses.php?uninstall=uninstall&ID=$ID\">";
		echo "<b>".$lang["buttons"][5]."</b></a>";
		echo "</td></tr>";

		$i++;		
	}
	echo "<tr class='tab_bg_1'><td align='center'>";
	echo "<input type='hidden' name='cID' value='$instID'>";
		dropdownSoftware();
	echo "</td><td align='center' class='tab_bg_2'>";
	echo "<input type='submit' name='select' value=\"".$lang["buttons"][4]."\" class='submit'>";
	echo "</td></tr>";
        echo "</table></center>";
	echo "</form>";

}

function countInstallations($sID) {
	
	GLOBAL $cfg_layout, $lang;
	
	$db = new DB;
	
	$query = "SELECT ID,serial FROM glpi_licenses WHERE (sID = '$sID')";
	$result = $db->query($query);

	if ($db->numrows($result)!=0) {

		if ($db->result($result,0,"serial")!="free") {
	
			// Get total
			$total = $db->numrows($result);
	
			// Get installed
			$i=0;
			$installed = 0;
			while ($i < $db->numrows($result))
			{
				$lID = $db->result($result,$i,"ID");
				$query2 = "SELECT license FROM glpi_inst_software WHERE (license = '$lID')";
				$result2 = $db->query($query2);
				$installed += $db->numrows($result2);
				$i++;
			}
		
			// Get remaining
			$remaining = $total - $installed;

			// Output
			echo "<table width='100%' cellpadding='2' cellspacing='0'><tr>";
			echo "<td>".$lang["software"][19].": <b>$installed</b></td>";
			if ($remaining < 0) {
				$remaining = "<span class='red'>$remaining";
				$remaining .= "</span>";
			} else if ($remaining == 0) {
				$remaining = "<span class='green'>$remaining";
				$remaining .= "</span>";
			} else {
				$remaining = "<span class='blue'>$remaining";
				$remaining .= "</span>";
			}			
			echo "<td>".$lang["software"][20].": <b>$remaining</b></td>";
			echo "<td>".$lang["software"][21].": <b>".$total."</b></td>";
			echo "</tr></table>";
		} else {
			// Get installed
			$i=0;
			$installed = 0;
			while ($i < $db->numrows($result))
			{
				$lID = $db->result($result,$i,"ID");
				$query2 = "SELECT license FROM glpi_inst_software WHERE (license = '$lID')";
				$result2 = $db->query($query2);
				$installed += $db->numrows($result2);
				$i++;
			}
			echo "<center><i>free software</i>&nbsp;&nbsp;".$lang["software"][19].": <b>$installed</b></center>";
		}
	} else {
			echo "<center><i>no licenses</i></center>";
	}
}	

?>
