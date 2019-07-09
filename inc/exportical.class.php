<?php
/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2014 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

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
 along with GLPI. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

/** @file
* @brief
*/
//[INICIO] CH48 : Descarga de calendarios ical por grupos 13/09/2017

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/**
 * Planning Class
**/
class Exportical extends CommonGLPI {

   static $rightname = 'planning';

   //*******************************************************************************************************************************
   // *********************************** Implementation ICAL ***************************************************************
   //*******************************************************************************************************************************

   /**
    *  Generate ical file content
    *
    * @param $who             user ID
    * @param $who_group       group ID
    * @param $limititemtype   itemtype only display this itemtype (default '')
    *
    * @return icalendar string
   **/
   static function generateIcal($who_groups, $limititemtype='') {
      global $CFG_GLPI;

	  
      include_once (GLPI_ROOT . "/lib/icalcreator/iCalcreator.class.php");
      $v = new vcalendar();

      if (!empty( $CFG_GLPI["version"])) {
         $v->setConfig( 'unique_id', "GLPI-Planning-".trim($CFG_GLPI["version"]) );
      } else {
         $v->setConfig( 'unique_id', "GLPI-Planning-UnknownVersion" );
      }

      $tz     = date_default_timezone_get();
      $v->setConfig( 'TZID', $tz );

      $v->setProperty( "method", "PUBLISH" );
      $v->setProperty( "version", "2.0" );

      $v->setProperty( "X-WR-TIMEZONE", $tz );
      $xprops = array( "X-LIC-LOCATION" => $tz );
      iCalUtilityFunctions::createTimezone( $v, $tz, $xprops );

      //$v->setProperty( "x-wr-calname", "GLPI-".$who."-".$who_group );
	  $v->setProperty( "x-wr-calname", "GLPI-ICAL" );
      $v->setProperty( "calscale", "GREGORIAN" );
      $interv = array();

      $begin  = time()-MONTH_TIMESTAMP*12;
      $end    = time()+MONTH_TIMESTAMP*12;
      $begin  = date("Y-m-d H:i:s", $begin);
      $end    = date("Y-m-d H:i:s", $end);
	  $who=0;
		foreach ($who_groups as $who_group) {
			/*
			echo "===========<br>";
			echo "Grupo :". $who_group;
			echo "<br>===========<br>";
			*/
			  $interv = array();
			  $params = array('who'       => $who,
							  'who_group' => $who_group,
							  'begin'     => $begin,
							  'end'       => $end);

			  if (empty($limititemtype)) {
				 foreach ($CFG_GLPI['planning_types'] as $itemtype) {
					 /*
					 echo $itemtype."--<br>";
					 print_r($itemtype::populatePlanning($params));
					 echo "<br>-------<br>";
					 */
					$interv = array_merge($interv, $itemtype::populatePlanning($params));
				 }
			  } else {
				 $interv = $limititemtype::populatePlanning($params);
			  }
				/*
				echo "------<br>";
				print_r($interv);
				echo "------<br>";
				*/

				
			  if (count($interv) > 0) {
				 foreach ($interv as $key => $val) {
					$vevent = new vevent(); //initiate EVENT
					if (isset($val['itemtype'])) {
						/*
						echo $val['itemtype']."--<br>";
						echo $key."--<br>";
						*/
					   if (isset($val[getForeignKeyFieldForItemType($val['itemtype'])])) {
						  $vevent->setProperty("uid",
											   $val['itemtype']."#".
												  $val[getForeignKeyFieldForItemType($val['itemtype'])]);
					   } else {
						  $vevent->setProperty("uid", "Other#".$key);
					   }
					} else {
					   $vevent->setProperty("uid", "Other#".$key);
					}

					$vevent->setProperty( "dstamp", $val["begin"] );
					$vevent->setProperty( "dtstart", $val["begin"] );
					$vevent->setProperty( "dtend", $val["end"] );

					if (isset($val["tickets_id"])) {
					   $vevent->setProperty("summary",
						  // TRANS: %1$s is the ticket, %2$s is the title
											sprintf(__('Ticket #%1$s %2$s'),
												   $val["tickets_id"], $val["name"]));
					} else if (isset($val["name"])) {
					   $vevent->setProperty( "summary", $val["name"] );
					}

					if (isset($val["content"])) {
					   $text = $val["content"];
					   // be sure to replace nl by \r\n
					   $text = preg_replace("/<br( [^>]*)?".">/i", "\r\n", $text);
					   $text = Html::clean($text);
					   $vevent->setProperty( "description", $text );
					} else if (isset($val["name"])) {
					   $text = $val["name"];
					   // be sure to replace nl by \r\n
					   $text = preg_replace("/<br( [^>]*)?".">/i", "\r\n", $text);
					   $text = Html::clean($text);
					   $vevent->setProperty( "description", $text );
					}

					if (isset($val["url"])) {
					   $vevent->setProperty("url",$val["url"]);
					}
					/*
					echo "<br>Evento ========<br>";
					print_r($vevent);
					echo "<br>========<br>";*/
					$objevento = $vevent->getProperty('uid');
					//echo $objevento;
					//echo "<br>========<br>";
					$chkcomp = $v->getComponent($objevento);
					if (empty($chkcomp))
					{
						$v->setComponent( $vevent );
					}
				
				 }
			  }
			  
		  	
			//echo "<br>Resultado========<br>";
			//print_r($v);
		}
	  /*
	  echo "<br>Resultado========<br>";
	  print_r($v);*/		
      $v->sort();
	  //exit();
//       $v->parse();
      return $v->returnCalendar();
   }

}
?>
