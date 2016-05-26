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


/**
 * Class for update menus
 */

dol_include_once('/ecommerce/core/modules/modECommerce.class.php');
dol_include_once('/ecommerce/class/data/eCommerceSite.class.php');

class eCommerceMenu
{
	private $module;
	private $db;
	private $siteDb;
	
    function eCommerceMenu($db, $siteDb=null, $modECommerce=null)
    {
    	$this->db = $db;
    	
    	if ($siteDb==null)
    		$this->siteDb = new eCommerceSite($this->db);
    	else
    		$this->siteDb = $siteDb;
    	
    	if ($modECommerce!=null)
        	$this->module = $modECommerce;
        
        return 1;
    }
    /**
     * @return array menu
     */
    function getMenu()
    {
    	$menu = array();
    	
    	//define top menu
    	$menu[0]=array(	'fk_menu'=>'',
    					'type'=>'top',
    					'titre'=>'ECommerceMenu',
    					'mainmenu'=>'ecommerce',
    					'leftmenu'=>'ecommerce',
    					'url'=>'/ecommerce/index.php',
    					'langs'=>'ecommerce@ecommerce',
    					'position'=>100,
    					'enabled'=>'$conf->ecommerce->enabled',
    					'perms'=>'$user->rights->ecommerce->read',
    					'target'=>'',
    					'user'=>2);
    	//define left menu
    	$menu[1]=array(	'fk_menu'=>'fk_mainmenu=ecommerce',
    					'type'=>'left',
    					'titre'=>'ECommerceMenu',
    					'mainmenu'=>'ecommerce',
    	                'leftmenu'=>'ecommerce',
    					'url'=>'/ecommerce/index.php',
    					'langs'=>'ecommerce@ecommerce',
    					'position'=>100,
    					'enabled'=>'$conf->ecommerce->enabled',
    					'perms'=>'$user->rights->ecommerce->read',
    					'target'=>'',
    					'user'=>2);
    	
    	//add link to configuration
    	$menu[2]=array(	'fk_menu'=>'fk_mainmenu=ecommerce',
    					'type'=>'left',
    					'titre'=>'ECommerceSetupSites',
    					'mainmenu'=>'ecommerce',
    	                'leftmenu'=>'ecommerce',
    					'url'=>'/ecommerce/admin/eCommerceSetup.php',
    					'langs'=>'ecommerce@ecommerce',
    					'position'=>110,
    					'enabled'=>'$conf->ecommerce->enabled',
    					'perms'=>'$user->rights->ecommerce->site',
    					'target'=>'',
    					'user'=>2);
    	
    	//add submenu foreach site
    	$sites = $this->siteDb->listSites();
    	if (count($this->siteDb))
    		foreach ($sites as $site)
    			$menu[]=array(	'fk_menu'=>'fk_mainmenu=ecommerce',
    							'type'=>'left',
    							'titre'=>$site['name'],
    							'mainmenu'=>'ecommerce',
    			                'leftmenu'=>'ecommerce',
    							'url'=>'/ecommerce/site.php?id='.$site['id'],
    							'langs'=>'ecommerce@ecommerce',
    							'position'=>120,
    							'enabled'=>'$conf->ecommerce->enabled',
    							'perms'=>'$user->rights->ecommerce->read',
    							'target'=>'',
    							'user'=>2);
    	return $menu;
    }
    
    /**
     * Update menu into database
     */
    function updateMenu()
    {
    	if ($this->module == null)
        	$this->module = new modECommerce($this->db);
        	
    	$this->module->menu = $this->getMenu();
    	$this->module->db->begin();
		$this->module->delete_menus();
		$this->module->insert_menus();
		$this->module->db->commit();
		return 1;
    }
}

