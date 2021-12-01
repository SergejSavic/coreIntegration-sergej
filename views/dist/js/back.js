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
    let initialSyncInterval;

    if (loginButton !== null) {
        loginButton.addEventListener('click', function () {
            myWindow = window.open(cleverReachURL, 'popUpWindow', 'location=yes,height=570,width=900,scrollbars=yes,status=yes');
            interval = setInterval(checkIfConnectTaskIsCompleted, 10);
        });
    }

    if (containerSync !== null) {
        initialSyncInterval = setInterval(checkInitialSyncStatus, 500);
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
                if (data === true) {
                    myWindow.close();
                    clearInterval(interval);
                    location.reload();
                }
            }
        });
    }

    function checkInitialSyncStatus() {
        $.ajax({
            type: 'POST',
            cache: false,
            dataType: 'json',
            url: adminAjaxLink,
            data: {
                ajax: true,
                action: 'checkinitialsyncstatus'
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
            clearInterval(initialSyncInterval);
            if (data === DONE) {
                spanSyncStatus.classList.add('done-sync');
                data = 'DONE';
            } else {
                spanSyncStatus.classList.add('error-sync');
                data = 'ERROR';
            }
        }
        spanSyncStatus.innerHTML = data;
    }
});
