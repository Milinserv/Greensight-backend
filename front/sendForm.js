$(document).ready(function() {
    $('#btn').click(function () {
        const data = {
            name: $("#name").val(),
            surname: $("#surname").val(),
            email: $("#email").val(),
            password: $("#password").val(),
            passwordConfirm: $("#passwordConfirm").val()
        };
        $.ajax({
            url: 'back/registration.php',
            type: "POST",
            data: data,
            success: function (data) {
                const res = JSON.parse(data);

                if (res.error) {
                    $("#error").css("visibility", "visible");
                    res.error instanceof Array
                        ? res.error.map((item) => {
                            $('#errorList').append('<li>' + item + '</li>');
                        })
                        : $('#errorList').append('<li>' + res.error + '</li>');
                }
                if (res.state) {
                    $("#form").css("display", "none");
                    $("#success-registration").css("visibility", "visible");
                }
            },
            error: function () {
                alert('Произошла ошибка');
            }
        });
    });
});