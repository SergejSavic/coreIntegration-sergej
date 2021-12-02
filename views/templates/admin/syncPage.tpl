<head></head>
<body id="container-body">
<div id="container-sync" class="container-sync">
    <img id="header-image-sync" class="header-image-sync" src={$headerImage}>
    <p id="clientid-sync" class="clientid-sync">{l s='Cliend Id :' mod='demo-sergej'} {$clientID}</p>
    <div id="content-container-sync" class="content-container-sync">
        <p id="paragraph-sync" class="paragraph-sync"><span class="sync-status">{l s='Sync Status :' mod='demo-sergej'}</span>
            <span id="span-sync-status" class="in-progress-sync">{l s='IN PROGRESS' mod='demo-sergej'}</span></p>
        <button id="submit-btn-sync" class="submit-btn-sync disable" type="submit">{l s='Synchronize' mod='demo-sergej'}</button>
    </div>
</div>
</body>
