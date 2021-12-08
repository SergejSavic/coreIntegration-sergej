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
<div id="loader" class="hide"></div>
<body id="container-body">
<div id="container" class="container border">
    <img id="header-image" class="header-image" src={$headerImage}>
    <div id="content-container" class="content-container">
        <img id="content-image" class="content-image" src={$contentImage}>
        <h2 id="welcome-header" class="welcome-header">{l s='Welcome to CleverReach' mod='core'}</h2>
        <div class="connect-div" id="connect-div">
            <p id="connect-paragraph" class="connect-paragraph">{l s='Connect, sync customer data and create' mod='core'}
                <span class="first">{l s='appealing emails to showcase your' mod='core'}</span>
                <span class="second">{l s='products to your customers.' mod='core'}</span></p>
        </div>
        <button id="submit-btn" class="submit-btn" type="submit">{l s='Connect' mod='core'}</button>
    </div>
</div>
</body>
</html>