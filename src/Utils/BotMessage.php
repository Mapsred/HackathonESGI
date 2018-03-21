<?php

namespace App\Utils;

/**
 * Interface BotMessage
 * @package App\Utils
 */
interface BotMessage
{
    const WRONG_FORMED_MESSAGE = "Désolé, je n'ai pas compris votre demande, pourriez-vous reformuler ?";

    const EXISTING_PROFILE = 'Désolé, un profil avec l\'identifiant <b>%s</b> existe dèjà';
    const CREATING_PROFILE = 'Bien reçu, je vous crée un profil avec l\'identifiant <b>%s</b>';

    const NON_EXISTING_PROFILE = 'Désolé, le profil avec l\'identifiant <b>%s</b> n\'existe pas';
    const USING_PROFILE = 'Bien reçu, vous êtes maintenant connectés avec l\'identifiant <b>%s</b>';

    const DATE_NOT_UNDERSTANDED = 'Désolé, je n\'ai pas bien compris votre date, pourriez vous essayer de reformuler différemment ?';
    const DATE_ALREADY_USED = 'Désolé, vous avez déjà un rendez-vous à cette date';
    const DATE_SUCCESS = 'Votre rendez-vous du %s a bien été ajouté';
    const TASK_NOT_LOGGED_IN = "Désolé, vous devez être connecté pour ajouter une tâche";
}