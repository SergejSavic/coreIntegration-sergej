/**
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
document.addEventListener("DOMContentLoaded", function (event) {
    const IN_PROGRESS = 'in_progress';
    const DONE = 'completed';
    const QUEUED = 'queued';
    const ERROR = 'failed';
    let loginButton = document.getElementById('submit-btn');
    let containerSync = document.getElementById('container-sync');
    let spanSyncStatus = document.getElementById('span-sync-status');
    let syncButton = document.getElementById('submit-btn-sync');
    let interval;
    let queueInterval;
    let syncInterval;
    let loader = document.getElementById('loader');
    let adminPageBody = document.getElementById('container-body');

    if (loginButton !== null) {
        loginButton.addEventListener('click', function () {
            myWindow = window.open(cleverReachURL, 'popUpWindow', 'location=yes,height=570,width=900,scrollbars=yes,status=yes');
            interval = setInterval(checkIfConnectTaskIsCompleted, 500);
            queueInterval = setInterval(checkIfConnectTaskIsQueued, 250);
        });
    }

    if (containerSync !== null) {
        syncInterval = setInterval(function() { checkSyncStatus('InitialSyncTask'); }, 500);
    }

    function checkIfConnectTaskIsCompleted() {
        $.ajax({
            type: 'POST',
            cache: false,
            dataType: 'json',
            url: adminAjaxLink,
            data: {
                ajax: true,
                action: 'checkifconnecttaskiscompleted'
            },
            success: function (data) {
                console.log(data);
                if (data === true) {
                    myWindow.close();
                    clearInterval(interval);
                    location.reload();
                }
            }
        });
    }

    function checkIfConnectTaskIsQueued() {
        $.ajax({
            type: 'POST',
            cache: false,
            dataType: 'json',
            url: adminAjaxLink,
            data: {
                ajax: true,
                action: 'checkifconnecttaskisqueued'
            },
            success: function (data) {
                console.log(data);
                if (data === true) {
                    myWindow.close();
                    loader.classList.remove('hide');
                    adminPageBody.classList.add('opacity');
                    clearInterval(queueInterval);
                }
            }
        });
    }

    function checkSyncStatus(taskType) {
        $.ajax({
            type: 'POST',
            cache: false,
            dataType: 'json',
            url: adminAjaxLink,
            data: {
                ajax: true,
                action: 'checksyncstatus',
                taskType: taskType
            },
            success: function (data) {
                console.log(data);
                editSyncTemplate(data);
            }
        });
    }

    function editSyncTemplate(data) {
        if (data === IN_PROGRESS || data === QUEUED) {
            spanSyncStatus.classList.add('in-progress-sync');
            syncButton.classList.add('disable');
            data = 'IN PROGRESS';
        } else {
            spanSyncStatus.classList.remove('in-progress-sync');
            syncButton.classList.remove('disable');
            clearInterval(syncInterval);
            if (data === DONE) {
                spanSyncStatus.classList.add('done-sync');
                data = 'DONE';
            } else {
                spanSyncStatus.classList.add('error-sync');
                data = 'ERROR';
            }
            syncButton.addEventListener("click", synchronize);
        }
        spanSyncStatus.innerHTML = data;
    }

    function synchronize() {
        spanSyncStatus.classList.remove('done-sync');
        spanSyncStatus.classList.remove('error-sync');
        spanSyncStatus.classList.add('in-progress-sync');
        spanSyncStatus.innerHTML = 'IN PROGRESS';
        syncInterval = setInterval(function() { checkSyncStatus('SecondarySyncTask'); }, 500);
        $.ajax({
            type: 'POST',
            cache: false,
            dataType: 'json',
            url: adminAjaxLink,
            data: {
                ajax: true,
                action: 'synchronize'
            }
        });
    }
});