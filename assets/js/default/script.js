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

    send: function () {
        Bot.sendButton.click(function () {
            var message = Bot.inputText.val();
            Bot.appendMessage(message, Bot.user);
            Bot.inputText.val("");

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

                    var blocked = 'Votre navigateur à bloqué le lancement, autorisez moi à le faire, s\'il vous plaît.';
                            
                        if(message['1']['type'] == 'Music'){

                            console.log(message['1']['info']);

                        var launch = window.open(message['1']['info'], '_blank');
                        window.blur();
                        window.focus();

                        if (!launch) {
                            Bot.appendMessage(blocked, "Djingo");
                        }

                        }


                    }

                }
            });
        });
    }
};