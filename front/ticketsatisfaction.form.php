<?php
/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2015-2016 Teclib'.

 http://glpi-project.org

 based on GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2014 by the INDEPNET Development Team.
 
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

include ('../inc/includes.php');

Session::checkLoginUser();

$inquest = new TicketSatisfaction();

if (isset($_POST["update"])) {
   $inquest->check($_POST["tickets_id"], UPDATE);
	// INICIO [CH35] CRI : que la estrella sea mayor que 0. 13/09/2017
	if ($_POST["satisfaction"] == 0)
	{
		Session::addMessageAfterRedirect(__('ERROR : Para registrar la encuesta es necesario indicar su nivel de satisfacci&oacute;n en la escala de 1 a 5 estrellas'), false, ERROR);
		Html::back();
	}
    // FINAL [CH35] CRI : que la estrella sea mayor que 0.   
   $inquest->update($_POST);

   Event::log($inquest->getField('tickets_id'), "ticket", 4, "tracking",
              //TRANS: %s is the user login
              sprintf(__('%s updates an item'), $_SESSION["glpiname"]));
   Html::back();
}

Html::displayErrorAndDie('Lost');
?>