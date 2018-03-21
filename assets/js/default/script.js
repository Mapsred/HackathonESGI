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
        Bot.user = "Invité";
        Bot.add = 0;
    },

    keyPress: function () {
        Bot.inputText.keyup(function (event) {
            if (event.keyCode === 13) {
                Bot.sendButton.click();
            }
        });
    },

    appendMessage: function (message, name) {
        var clone = Bot.modelDiv.clone();
        clone.find('.name').html(name);
        clone.find('.text-content').html(message);
        clone.appendTo('#messageContainer');
    },

    replaceName: function (name) {
        $('#messageContainer .message-model .name').each(function () {
            if ($(this).text() !== "Djingo") {
                $(this).html(name);
            }
        });
    },

    handleType: function (message) {

        var blocked = 'Votre navigateur à bloqué le lancement, autorisez moi à le faire, s\'il vous plaît.';

        if (typeof message['List'] !== "undefined") {

            Bot.appendMessage(message['List'], "Djingo"); // LISTE

        } else if (typeof message['Music'] !== "undefined") {

            console.log(message['Music']);
            var launch = window.open(message['Music'], '_blank'); // Jouer Musique
            window.blur();
            window.focus();

            if (!launch) {
                Bot.appendMessage(blocked, "Djingo");
            }

        } else if (typeof message['Add'] !== "undefined") {

           Bot.add++;

        }
    },

    addContent: function () {

        Bot.appendMessage('Merci, je procède à l\'ajout', "Djingo");

        Bot.add = 0;

        $.ajax({
            url: Routing.generate('add'),
            type: "POST",
            data: {'name': Bot.addName, 'url': Bot.addUrl},
            success: function (res) {
                console.log(res);
                var message = res['message'];
                Bot.appendMessage(message, "Djingo");
            }

        });

    },

    send: function () {
        Bot.sendButton.click(function () {
            var message = Bot.inputText.val();
            Bot.appendMessage(message, Bot.user);
            Bot.inputText.val("");

            if (Bot.add == 1)
            {
                Bot.addName = message;
                Bot.add++;
                Bot.appendMessage('Très bien, et quel est le lien ?', "Djingo");

            }
            else if (Bot.add == 2)
            {
                Bot.addUrl = message;
                Bot.addContent();
            }
            else
            {

            $.ajax({
                url: Routing.generate('query'),
                type: "POST",
                data: {'q': message},
                success: function (res) {
                    console.log(res);

                    Bot.user = res['name'];
                    Bot.replaceName(res['name']);

                    var message = res['message'];

                    Bot.appendMessage(message, "Djingo");

                    if(message.constructor === Array){                    

                    Bot.appendMessage(typeof message === "string" ? message : message[0], "Djingo");
                    if (typeof message === "object") {
                        Bot.handleType(message);
                    }   

                    }
                }
            });
            }
        
        });

    }

}