$(function() {
    var success_fun = function(data) {
        console.log("Authentication successful!");
        console.log("Authentication token:", data);
        $("#result_status").html("Success!");
        $("#token-raw").text(JSON.stringify(data));
        $("#token-content").text(JSON.stringify(jwt_decode(data), null, 2));
        $("#result_div").show();
        $("#jwt_result").attr("value", data);
    };
    var cancel_fun = function(data) {
        console.log("Authentication cancelled!");
        $("#result_status").html("Cancelled!");
    }
    var error_fun = function(data) {
        console.log("Authentication failed!");
        console.log("Error data:", data);
        $("#result_status").html("Failure!");
    }

    $("#irma_btn").on("click", function() {
        console.log("Button clicked");
        $.get("jwt.php", function(jwt) {
            IRMA.verify(jwt, success_fun, cancel_fun, error_fun);
        });
    });
});