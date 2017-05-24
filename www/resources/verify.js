$(function() {
    var success_fun = function(data) {
        console.log("IRMA authentication successful, submitting form");
        $("#jwt_result").attr("value", data);
        $("form#irma_result_form").submit();
    };
    var cancel_fun = function(data) {
        console.log("Authentication cancelled!");
        $("#irma_result").html('<div class="alert alert-warning" role="alert">IRMA authentication cancelled.</div>');
    }

    var error_fun = function(data) {
        console.log("Authentication failed!");
        console.log("Error data:", data);
        $("#irma_result").html('<div class="alert alert-danger" role="alert">The IRMA authentication has failed.</div>');
    }

    $("#irma_btn").on("click", function() {
        console.log("Button clicked");
        $.get("jwt.php", function(jwt) {
            IRMA.verify(jwt, success_fun, cancel_fun, error_fun);
        });
    });
});
