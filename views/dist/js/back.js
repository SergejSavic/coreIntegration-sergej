document.addEventListener("DOMContentLoaded", function (event) {
    let loginButton = document.getElementById('submit-btn');
    let interval;

    loginButton.addEventListener('click', function () {
        myWindow = window.open(cleverReachURL, 'popUpWindow', 'location=yes,height=570,width=900,scrollbars=yes,status=yes');
        interval = setInterval(chechIfConnectTaskIsCompleted, 50);
    });

    function chechIfConnectTaskIsCompleted() {
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
});
