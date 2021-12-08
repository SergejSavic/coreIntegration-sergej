{*
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2021 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<html>
<head>
    <title>Admin Page</title>
</head>
<body id="container-body">
<div id="container-sync" class="container-sync">
    <img id="header-image-sync" class="header-image-sync" src={$headerImage}>
    <p id="clientid-sync" class="clientid-sync">{l s='Cliend Id :' mod='core'} {$clientID}</p>
    <div id="content-container-sync" class="content-container-sync">
        <p id="paragraph-sync" class="paragraph-sync"><span class="sync-status">{l s='Sync Status :' mod='core'}</span>
            <span id="span-sync-status" class="in-progress-sync">{l s='IN PROGRESS' mod='core'}</span></p>
        <button id="submit-btn-sync" class="submit-btn-sync disable" type="submit">{l s='Synchronize' mod='core'}</button>
    </div>
</div>
</body>
</html>
