<?php
/*
 * @version $Id: requesttype.class.php 17152 2012-01-24 11:22:16Z moyo $
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2012 by the INDEPNET Development Team.

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

// ----------------------------------------------------------------------
// Original Author of file: olb26s
// CH11 Gobierno TI Nuevo tipo de seguimiento olb26s 11/09/2017
// ----------------------------------------------------------------------

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/// Class RequestType
class FollowupType extends CommonDropdown {


   static function getTypeName($nb=0) {
      return _n('Followup type', 'Followup types', $nb);
   }

   
   function getSearchOptions() {
      global $LANG;

      $tab = parent::getSearchOptions();

      return $tab;
   }   


   /**
    * Get the default request type for a given source (mail, helpdesk)
    *
    * @param $source string
    *
    * @return requesttypes_id
   **/
   static function getDefault($source) {
      global $DB;

      if (!in_array($source, array('mail', 'helpdesk'))) {
         return 0;
      }

      //foreach ($DB->request('glpi_followuptypes', array('is_'.$source.'_default' => 1)) as $data) {
      //   return $data['id'];
      //}
      return 0;
   }


   function cleanDBonPurge() {
      Rule::cleanForItemCriteria($this);
   }
}

?>