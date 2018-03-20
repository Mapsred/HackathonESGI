$(document).ready(function () {
    Bot.init();
});


var Bot = {
    init: function () {
        Bot.initParameters();
        Bot.keyPress();
        Bot.send();
    },

    initParameters: function () {
        Bot.inputText = $('#inputText');
        Bot.sendButton = $('#send');
        Bot.modelDiv = $(".model .message-model");
    },

    keyPress: function () {
        Bot.inputText.keyup(function (event) {
            if (event.keyCode === 13) {
                Bot.sendButton.click();
            }
        });
    },

    send: function () {
        Bot.sendButton.click(function () {

            var message = Bot.inputText.val();

            var clone = Bot.modelDiv.clone();
            clone.find('.name').html("Name");
            clone.find('.text-content').html(message);
            Bot.inputText.val("");

            clone.appendTo('#messageContainer');

            $.ajax({
                url: Routing.generate('query'),
                type: "POST",
                data: {'q': message},
                success: function (reponse) {
                    console.log(reponse);
                }
            });


        });
    }
};