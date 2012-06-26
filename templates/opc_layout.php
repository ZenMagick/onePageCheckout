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
<!doctype html>

	<html lang="en" class="no-js">
	<head>
    </head>
  <body id="b_<?php echo $request->getRequestId() ?>">
    <div id="bannerOne"><?php echo $this->fetchBlockGroup('banners.header1') ?></div>
		<div id="containerOuter">
    <div id="container">
			<div id="main" class="clearfix" role="main">
				<table id="contentMainWrapper" cellspacing="0" cellpadding="0" border="0" width="100%">
					<tr>
						<td id="navColumnCenter" valign="top">
							<div id="<?php echo $request->getRequestId()?>Default" class="centerColumn">

								<!-- <div id="bannerThree"><?php echo $this->fetchBlockGroup('banners.header3') ?></div> -->

								<?php if ($container->get('messageService')->hasMessages()) { ?>
										<ul id="messages">
										<?php foreach ($container->get('messageService')->getMessages() as $message) { ?>
												<li class="<?php echo $message->getType() ?>"><?php echo $message->getText() ?></li>
										<?php } ?>
										</ul>
								<?php } ?>

								<?php echo  $this->fetch($viewTemplate); ?>
								<div id="bannerFour"><?php echo $this->fetchBlockGroup('banners.footer1') ?></div>
							</div>
						</td>
					</tr>
				</table>

  </body>
  <!-- ajax method -->
</html>
