<?php

declare(strict_types=1);

/*
 * @addtogroup unitrigger
 * @{
 *
 * @package       UniTrigger
 * @file          module.php
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2019 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       1.5
 *
 */

require_once __DIR__ . '/../libs/UniversalTriggerBase.php';

/**
 * UniversalTrigger Klasse für die Nutzung der IPS Nachrichten in einem PHP-Script.
 * Erweitert UniversalTriggerBase.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2019 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 * @version       1.5
 *
 * @example <b>Ohne</b>
 *
 * @property int $OldObjectId
 * @property int $OldMessageId
 */
class UniversalTrigger extends UniversalTriggerBase
{
    /**
     * Interne Funktion des SDK.
     */
    public function Create()
    {
        parent::Create();
        $this->OldTrigger = 0;
        $this->RegisterPropertyInteger('ScriptID', 0);
        $this->RegisterPropertyInteger('ObjectId', 0);
        $this->RegisterPropertyInteger('MessageId', 10403);
        $this->SetStatus(IS_ACTIVE);
    }

    /**
     * Interne Funktion des SDK.
     */
    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        $this->FireTargetScript($TimeStamp, $SenderID, $Message, $Data);
    }

    /**
     * Interne Funktion des SDK.
     */
    public function ApplyChanges()
    {
        $this->UnregisterMessage($this->OldObjectId, $this->OldMessageId);
        $this->UnregisterReference($this->OldObjectId);
        parent::ApplyChanges();
        $NewObjectId = $this->ReadPropertyInteger('ObjectId');
        $NewMessageId = $this->ReadPropertyInteger('MessageId');
        $this->RegisterMessage($NewObjectId, $NewMessageId);
        $this->RegisterReference($NewObjectId);
        $this->OldObjectId = $NewObjectId;
        $this->OldMessageId = $NewMessageId;
    }

    public function GetConfigurationForm()
    {
        $Form = json_decode(file_get_contents(__DIR__ . '/form.json'), true);
        foreach (self::$Messages as $MessageId => $MessageName) {
            $Messages[] = ['label' => $MessageName, 'value' => $MessageId];
        }
        $Form['elements'][2]['options'] = $Messages;
        $this->SendDebug('Form', json_encode($Form), 0);
        return json_encode($Form);
    }

    //################# PRIVATE
}

/* @} */
