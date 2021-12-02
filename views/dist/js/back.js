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
