<?php
/*
 * ZenMagick - Smart e-commerce
 * Copyright (C) 2006-2010 zenmagick.org
 *
 * Portions Copyright (c) 2003 The zen-cart developers
 * Portions Copyright (c) 2003 osCommerce
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
?>
<div id="header">
		<div id="headerWrapperOuter" class="clearfix">
		<?php
			$geo = $session->getValue("geo", "ri");
			// BOF Rubikintegration
			$objLanguage = $session->getLanguage();
			$objLanguage->setCode(strtolower($geo['countryCode']));
			$session->setLanguage($objLanguage);

			// EOF Rubikintegration
		?>
			<div id="logo_header" class="back"></div><!--logo-->
			<div id="headerMenu" class="forward">
				<!--bof ajax search -->

				<!--eof ajax search -->
				<div id="currenciesDropDown" class="forward">
				</div><!--currenciesDropDown-->
				<div id="headerInner" class="forward clearfix">
					<div class="" id="leftMenu">
					</div>
				</div><!--headerInner-->
				<br class="clearBoth" />
			</div><!--headerMenu-->
		</div><!--headerWrapperOuter-->
</div>
