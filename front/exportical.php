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

include ('../inc/includes.php');

//$listgrupos[0]=126;
//$listgrupos[1]=326;
//aGrupo=[valor1,valor2,valor3]

if (isset($_GET['aGrupo']))
{
	$arraygrupo = json_decode($_GET['aGrupo']);
	if (empty($arraygrupo))
	{
		echo "No hay resultados";
		exit;
	}
	Exportical::generateIcal($arraygrupo);
}





?>