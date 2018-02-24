$(document).ready(function() {
    $("#toggleView").click(function() {
        $( ".hideTransaction" ).toggle();
    });

    $(".example").click(function() {
        $(this).next().toggle();
        console.log($(this).attr('data-year'));
    });
});
