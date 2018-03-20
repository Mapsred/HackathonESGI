$("button").click(function() {

    $.ajax({
        type: "POST",
        url: "/query?q=",
        dataType: 'json',
        success: function(reponse){
            console.log(reponse);
        }
    });

});