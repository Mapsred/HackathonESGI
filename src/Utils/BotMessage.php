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
}