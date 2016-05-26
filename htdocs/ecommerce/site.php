<?php
/* Copyright (C) 2010 Franck Charpentier - Auguria <franck.charpentier@auguria.net>
 * Copyright (C) 2013 Laurent Destailleur          <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */


$res=0;
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res && preg_match('/\/teclib([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
dol_include_once("/ecommerce/class/business/eCommerceSynchro.class.php");
$langs->load("ecommerce@ecommerce");
$errors = array();
$success = array();
$site = null;

$nbCategoriesToUpdate=0;
$nbProductInDolibarr=0;
$nbSocieteInDolibarr = 0;
$nbCommandeInDolibarr = 0;
$nbFactureInDolibarr = 0;
		
$langs->load("admin");
$langs->load("ecommerce");

// Protection if external user
if ($user->societe_id > 0 || !$user->rights->ecommerce->read)
{
	accessforbidden();
}

$id=GETPOST('id','int');
$error=0;


/*******************************************************************
* ACTIONS
********************************************************************/

if ($id)
{
	try
	{
		$site= new eCommerceSite($db);
		$site->fetch($id);
		if (isset($site->timeout))
			set_time_limit($site->timeout);
		
		$site->cleanOrphelins();	
		
	    $synchro = new eCommerceSynchro($db, $site);
	    
	    dol_syslog("Try to connect");
		$synchro->connect();
		if (count($synchro->errors))
		{
		    $error++;
		    setEventMessages($synchro->error, $synchro->errors, 'errors');
		}

		/*$result=0;
		
		if (! $error)
		{
		  $result=$synchro->checkAnonymous();
		}
		
		if ($result <= 0)
		{
		  $errors = $synchro->errors;
		  $errors[] = $synchro->error;
		  $error++;
		}*/
		
		//synch only with write rights
		if (! $error && $user->rights->ecommerce->write)
		{
			if (GETPOST('reset_data'))
			{
			    dol_syslog("**** dropImportedAndSyncData");
				$synchro->dropImportedAndSyncData();
			}

			if (GETPOST('submit_synchro_category') || GETPOST('submit_synchro_all'))
			{
			    dol_syslog("**** synchCategory");
				$synchro->synchCategory();
			}
			if (GETPOST('submit_synchro_product') || GETPOST('submit_synchro_all'))
			{
			    dol_syslog("**** synchProduct");
				$synchro->synchProduct();
			}
			if (GETPOST('submit_synchro_societe') || GETPOST('submit_synchro_all'))
			{
			    dol_syslog("**** synchSociete");
				$synchro->synchSociete();
			}
			if (GETPOST('submit_synchro_commande') || GETPOST('submit_synchro_all'))
			{
			    dol_syslog("**** synchCommande");
				$synchro->synchCommande();
			}
			if (GETPOST('submit_synchro_facture') || GETPOST('submit_synchro_all'))
			{
			    dol_syslog("**** synchFacture");
				$synchro->synchFacture();
			}
		}
	    
	    
    	/***************************************************
    	* Vars to build output tpl page
    	****************************************************/
        $nbCategoriesInDolibarr = $synchro->getNbCategoriesInDolibarr(true);
		if ($nbCategoriesInDolibarr < 0) $error++;
        $nbCategoriesInDolibarrLinkedToE = $synchro->getNbCategoriesInDolibarrLinkedToE($site->fk_cat_product);
		
        $nbProductInDolibarr = $synchro->getNbProductInDolibarr(true);
		if ($nbProductInDolibarr < 0) $error++;
		$nbProductInDolibarrLinkedToE = $synchro->getNbProductInDolibarrLinkedToE(true);
		
		$nbSocieteInDolibarr = $synchro->getNbSocieteInDolibarr(true);
		if ($nbSocieteInDolibarr < 0) $error++;
		$nbSocieteInDolibarrLinkedToE = $synchro->getNbSocieteInDolibarrLinkedToE(true);
		
		if (! empty($conf->commande->enabled))
		{
            $nbCommandeInDolibarr = $synchro->getNbCommandeInDolibarr(true);
            if ($nbCommandeInDolibarr < 0) $error++;
            $nbCommandeInDolibarrLinkedToE = $synchro->getNbCommandeInDolibarrLinkedToE(true);
		}
		if (! empty($conf->facture->enabled))
		{
			$nbFactureInDolibarr = $synchro->getNbFactureInDolibarr(true);
			if ($nbFactureInDolibarr < 0) $error++;
			$nbFactureInDolibarrLinkedToE = $synchro->getNbFactureInDolibarrLinkedToE(true);
		}

		if (! $error)
		{
			if (! $error) $nbCategoriesToUpdate = $synchro->getNbCategoriesToUpdate(true);
			if ($nbCategoriesToUpdate < 0) $error++;
			if (! $error) $nbProductToUpdate = $synchro->getNbProductToUpdate(true);
			if ($nbProductToUpdate < 0) $error++;
			if (! $error) $nbSocieteToUpdate = $synchro->getNbSocieteToUpdate(true);
			if ($nbSocieteToUpdate < 0) $error++;
			if (! empty($conf->commande->enabled))
            {
                if (! $error) $nbCommandeToUpdate = $synchro->getNbCommandeToUpdate(true);
                if ($nbCommandeToUpdate < 0) $error++;
            }
			if (! $error) $nbFactureToUpdate = $synchro->getNbFactureToUpdate(true);
			if ($nbFactureToUpdate < 0) $error++;

			if ($nbCategoriesToUpdate == 0 && $nbProductToUpdate == 0 && $nbSocieteToUpdate == 0 && $nbCommandeToUpdate == 0 && $nbFactureToUpdate == 0)
			{
			    $site->last_update = $synchro->toDate;
			    $site->update($user);
			}
			
			if ($user->rights->ecommerce->write)
				$synchRights = true;
			
			if (count($synchro->success))
				$success = $synchro->success;
			
			if (count($synchro->errors))
				$errors = $synchro->errors;
		}
	}
	catch (Exception $e)
	{
		$errors[] = $langs->trans('ECommerceSiteErrorConnect');
	}
}

/***************************************************
* Show page
****************************************************/
$urltpl=dol_buildpath('/ecommerce/tpl/site.tpl.php',0);
include($urltpl);

$db->close();
