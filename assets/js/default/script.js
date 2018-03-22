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
        Bot.messageContainer = $('#messageContainer');
        Bot.user = "Invité";
        Bot.add = 0;
        Bot.initRoutineParameters();
    },

    initRoutineParameters: function () {
        Bot.routine = {
            isOnRoutine: 0,
            routineContent: [],
            routineName: ""
        };
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
        Bot.messageContainer.animate({
            scrollTop: Bot.messageContainer.prop("scrollHeight") - Bot.messageContainer.height()
        }, 200);
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
            console.log(message['Add']);
            if (message['Add'] = 'Musique')
            {
                Bot.addType = 'Music';
            }
            

        } else if (typeof message['AddRoutine'] !== "undefined") {
            Bot.routine.isOnRoutine = 1;
            Bot.routine.routineName = message['AddRoutine'];
        }else if (typeof message['LaunchRoutine'] !== "undefined") {
            $.each(message['LaunchRoutine'], function (key, message) {
                Bot.query(message);
            });
        }
    },

    addContent: function () {
        Bot.appendMessage('Merci, je procède à l\'ajout', "Djingo");
        Bot.add = 0;

        $.ajax({
            url: Routing.generate('add'),
            type: "POST",
            data: {'name': Bot.addName, 'url': Bot.addUrl, 'type' : Bot.addType},
            success: function (res) {
                console.log(res);
                var message = res['message'];
                Bot.appendMessage(message, "Djingo");
            }
        });
    },

    query: function (message) {
        $.ajax({
            url: Routing.generate('query'),
            type: "POST",
            data: {'q': message},
            success: function (res) {
                console.log(res);
                Bot.user = res['name'];
                Bot.replaceName(res['name']);

                var message = res['message'];
                Bot.appendMessage(typeof message === "string" ? message : message[0], "Djingo");
                if (typeof message === "object") {
                    Bot.handleType(message);
                }
            }
        });
    },

    manageRoutine: function (message) {
        if (message.length > 0) {
            Bot.routine.routineContent.push(message);
            Bot.appendMessage('Quelle autre action voulez-vous ajouter ?  (appuyez sur entrée pour arreter l\'ajout)', "Djingo");
        } else {
            $.ajax({
                url: Routing.generate('routine_add'),
                type: "POST",
                data: {'name': Bot.routine.routineName, 'content': Bot.routine.routineContent},
                success: function (res) {
                    Bot.initRoutineParameters();
                    var message = res['message'];
                    Bot.appendMessage(message, "Djingo");
                }
            });
        }

    },

    send: function () {
        Bot.sendButton.click(function () {
            var message = Bot.inputText.val();
            Bot.appendMessage(message, Bot.user);
            Bot.inputText.val("");

            if (Bot.routine.isOnRoutine !== 0) {
                Bot.manageRoutine(message);
            } else {
                if (Bot.add === 1) {
                    Bot.addName = message;
                    Bot.add++;
                    Bot.appendMessage('Très bien, et quel est le lien ?', "Djingo");
                } else if (Bot.add === 2) {
                    Bot.addUrl = message;
                    Bot.addContent();
                } else {
                    Bot.query(message);
                }
            }
        });
    }
};