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


llxHeader();

print_fiche_titre($langs->trans("ECommerceSetup"),$linkback,'setup');

?>
	<script type="text/javascript" src="<?php print dol_buildpath('/ecommerce/js/form.js',1); ?>"></script>
	<br/>
	<form id="site_form_select" name="site_form_select" action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
		<select class="flat" id="site_form_select_site" name="site_form_select_site" onchange="eCommerceSubmitForm('site_form_select')">
			<option value="0"><?php print $langs->trans('ECommerceAddNewSite') ?></option>
<?php
if (count($sites))
	foreach ($sites as $option)
	{
		print '
			<option';
		if ($ecommerceId == $option['id'])
			print ' selected="selected"';
		print ' value="'.$option['id'].'">'.$option['name'].'</option>';
	}
?>
		</select>
	</form>
	<br/>
<?php print_titre($title); ?>
	<form name="site_form_detail" id="site_form_detail" action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
			<input type="hidden" name="token" value="<?php print $_SESSION['newtoken'] ?>">
			<input id="site_form_detail_action" type="hidden" name="site_form_detail_action" value="save">
			<input type="hidden" name="ecommerce_id" value="<?php print $ecommerceId ?>">
			<input type="hidden" name="ecommerce_last_update" value="<?php print $ecommerceLastUpdate ?>">
			<table class="noborder" width="100%">
				<tr class="liste_titre">
					<td width="20%"><?php print $langs->trans('Parameter') ?></td>
					<td><?php print $langs->trans('Value') ?></td>
					<td><?php print $langs->trans('Description') ?></td>
				</tr>
<?php
$var=!$var;
?>
				<tr <?php print $bc[$var] ?>>
					<td><span class="fieldrequired"><?php print $langs->trans('ECommerceSiteName') ?></span></td>
					<td>
						<input type="text" class="flat" name="ecommerce_name" value="<?php print $ecommerceName ?>" size="30">
					</td>
					<td><?php print $langs->trans('ECommerceSiteNameDescription') ?></td>
				</tr>
<?php
$var=!$var;
?>
				<tr <?php print $bc[$var] ?>>
					<td><span class="fieldrequired"><?php print $langs->trans('ECommerceCatProduct') ?></span></td>
					<td>
						<select class="flat" name="ecommerce_fk_cat_product">
							<option value="0">&nbsp;</option>
							<?php
								if (count($productCategories))
									foreach ($productCategories as $productCategorie)
									{
										print '
											<option';
										if ($ecommerceFkCatProduct == $productCategorie['id'])
											print ' selected="selected"';
										print ' value="'.$productCategorie['id'].'">'.$productCategorie['label'].'</option>';
									}
								?>
						</select>
					</td>
					<td><?php print $langs->trans('ECommerceCatProductDescription') ?></td>
				</tr>
<?php
$var=!$var;
?>
				<tr <?php print $bc[$var] ?>>
					<td><span class="fieldrequired"><?php print $langs->trans('ECommerceCatSociete') ?></span></td>
					<td>
						<select class="flat" name="ecommerce_fk_cat_societe">
							<option value="0">&nbsp;</option>
							<?php
								if (count($productCategories))
									foreach ($societeCategories as $societeCategorie)
									{
										print '
											<option';
										if ($ecommerceFkCatSociete == $societeCategorie['id'])
											print ' selected="selected"';
										print ' value="'.$societeCategorie['id'].'">'.$societeCategorie['label'].'</option>';
									}
								?>
						</select>
					</td>
					<td><?php print $langs->trans('ECommerceCatSocieteDescription') ?></td>
				</tr>
<?php
$var=!$var;
?>
				<!-- Filter are not used at this time
				<tr <?php print $bc[$var] ?>>
					<td><?php print $langs->trans('ECommerceFilterLabel') ?></td>
					<td>
						<input type="text" class="flat" name="ecommerce_filter_label" value="<?php print $ecommerceFilterLabel ?>" size="30">
					</td>
					<td><?php print $langs->trans('ECommerceFilterLabelDescription') ?></td>
				</tr>
<?php
$var=!$var;
?>
				<tr <?php print $bc[$var] ?>>
					<td><?php print $langs->trans('ECommerceFilterValue') ?></td>
					<td>
						<input type="text" class="flat" name="ecommerce_filter_value" value="<?php print $ecommerceFilterValue ?>" size="30">
					</td>
					<td><?php print $langs->trans('ECommerceFilterValueDescription') ?></td>
				</tr>
<?php
$var=!$var;
?>
				-->
				<tr <?php print $bc[$var] ?>>
					<td><span class="fieldrequired"><?php print $langs->trans('ECommerceSiteType') ?></span></td>
					<td>
						<select class="flat" name="ecommerce_type">
							<option value="0">&nbsp;</option>
							<?php
								if (count($siteTypes))
									foreach ($siteTypes as $key=>$value)
									{
										print '
											<option';
										if ($ecommerceType == $key)
											print ' selected="selected"';
										print ' value="'.$key.'">'.$langs->trans($value).'</option>';
									}
								?>
						</select>
					</td>
					<td><?php print $langs->trans('ECommerceSiteTypeDescription') ?></td>
				</tr>
<?php
$var=!$var;
?>
				<tr <?php print $bc[$var] ?>>
					<td><span class="fieldrequired"><?php print $langs->trans('ECommerceSiteAddress') ?></span></td>
					<td>
						<input type="text" class="flat" name="ecommerce_webservice_address" value="<?php print $ecommerceWebserviceAddress ?>" size="60">
						<?php 
						if ($ecommerceWebserviceAddress)
						    print '<br><a href="'.$ecommerceWebserviceAddress.'" target="_blank">'.$langs->trans("ECommerceClickUrlToTestUrl").'</a>';
						?>
					</td>
					<td><?php print $langs->trans('ECommerceSiteAddressDescription') ?></td>
				</tr>
<?php
$var=!$var;
?>
				<tr <?php print $bc[$var] ?>>
					<td class="fieldrequired"><?php print $langs->trans('ECommerceUserName') ?></td>
					<td>
						<input type="text" class="flat" name="ecommerce_user_name" value="<?php print $ecommerceUserName ?>" size="20">
					</td>
					<td><?php print $langs->trans('ECommerceUserNameDescription') ?></td>
				</tr>
<?php
$var=!$var;
?>
				<tr <?php print $bc[$var] ?>>
					<td class="fieldrequired"><?php print $langs->trans('ECommerceUserPassword') ?></td>
					<td>
						<input type="password" class="flat" name="ecommerce_user_password" value="<?php print $ecommerceUserPassword ?>" size="20">
					</td>
					<td><?php print $langs->trans('ECommerceUserPasswordDescription') ?></td>
				</tr>
<?php
$var=!$var;
?>
				<tr <?php print $bc[$var] ?>>
					<td><span><?php print $langs->trans('ECommerceTimeout') ?></span></td>
					<td>
						<input type="text" class="flat" name="ecommerce_timeout" value="<?php print $ecommerceTimeout ?>" size="10">
					</td>
					<td><?php print $langs->trans('ECommerceTimeoutDescription') ?></td>
				</tr>
<?php
$var=!$var;
?>
				<tr <?php print $bc[$var] ?>>
					<td><?php print $langs->trans('ECommerceMagentoUseSpecialPrice') ?></td>
					<td>
						<input type="checkbox" class="flat" name="ecommerce_magento_use_special_price" <?php print ($ecommerceMagentoUseSpecialPrice ? 'checked' : '') ?> />
					</td>
					<td><?php print $langs->trans('ECommerceMagentoUseSpecialPriceDescription') ?></td>
				</tr>
<?php
$var=!$var;
?>
				<tr <?php print $bc[$var] ?>>
					<td><?php print $langs->trans('ECommerceMagentoPriceType') ?></td>
					<td>						
						<select class="flat" name="ecommerce_magento_price_type">
							<option value="HT" <?php print ($ecommerceMagentoPriceType == 'HT' ? 'selected="selected"' : '') ?>><?php print $langs->trans('ECommerceMagentoPriceTypeHT') ?></option>
							<option value="TTC"<?php print ($ecommerceMagentoPriceType == 'TTC' ? 'selected="selected"' : '') ?>><?php print $langs->trans('ECommerceMagentoPriceTypeTTC') ?></option>
						</select>						
					</td>
					<td><?php print $langs->trans('ECommerceMagentoPriceTypeDescription') ?></td>
				</tr>
			</table>
			<br/>
			<center>
<?php
if ($siteDb->id)
{
?>
				<input type="submit" name="save_site" class="butAction" value="<?php print $langs->trans('Save') ?>">
				<a class="butActionDelete" href='javascript:eCommerceConfirmDelete("site_form_detail", "<?php print $langs->trans('ECommerceConfirmDelete') ?>")'><?php print $langs->trans('Delete') ?></a>
<?php
}
else
{
?>
				<input type="submit" name="save_site" class="butAction" value="<?php print $langs->trans('Add') ?>">
<?php 
}
?>
			</center>
		</form>

<?php
if ($errors != array())
	foreach ($errors as $error)
		print '<p class="error">'.$error.'</p>';
if ($success != array())
	foreach ($success as $succes)
		print '<p class="ok">'.$succes.'</p>';
?>
		<br/>
				