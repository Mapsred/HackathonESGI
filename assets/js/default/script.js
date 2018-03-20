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
            var clone = Bot.modelDiv.clone();
            clone.find('.name').html("Name");
            clone.find('.text-content').html(Bot.inputText.val());
            Bot.inputText.val("");

            clone.appendTo('#messageContainer');
        });
    }
};