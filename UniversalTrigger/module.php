<?php

/*
 * @addtogroup unitrigger
 * @{
 *
 * @package       UniTrigger
 * @file          module.php
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2018 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       1.0
 *
 */

require_once(__DIR__ . "/../libs/UniversalTriggerBase.php");

/**
 * NoTrigger Klasse für die die Überwachung von mehreren Variablen auf fehlende Änderung/Aktualisierung.
 * Erweitert NoTriggerBase.
 *
 * @package       UniTrigger
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2018 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       1.0
 * @example <b>Ohne</b>
 *
 * @property int $OldObjectId
 * @property int $OldMessageId
 */
class UniversalTrigger extends UniversalTriggerBase
{
    /**
     * Interne Funktion des SDK.
     *
     * @access public
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
     *
     * @access public
     */
    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        $this->SendDebug('Message:TimeStamp', $TimeStamp, 0);
        $this->SendDebug('Message:SenderID', $SenderID, 0);
        $this->SendDebug('Message:Message', $Message, 0);
        $this->SendDebug('Message:Data', $Data, 0);
        $this->FireTargetScript($TimeStamp, $SenderID, $Message, $Data);
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function ApplyChanges()
    {
        $this->UnregisterMessage($this->OldObjectId, $this->OldMessageId);
        parent::ApplyChanges();
        $NewObjectId = $this->ReadPropertyInteger('ObjectId');
        $NewMessageId = $this->ReadPropertyInteger('MessageId');
        $this->RegisterMessage($NewObjectId, $NewMessageId);
        $this->OldObjectId = $NewObjectId;
        $this->OldMessageId = $NewMessageId;
    }

    public function GetConfigurationForm()
    {
        $Form = json_decode(file_get_contents(__DIR__ . "/form.json"), true);
        foreach (self::$Messages as $MessageId => $MessageName) {
            $Messages[] = array('label' => $MessageName, 'value' => $MessageId);
        }
        $Form['elements'][2]['options'] = $Messages;
        $this->SendDebug('Form', json_encode($Form), 0);
        return json_encode($Form);
    }

    ################## PRIVATE
}

/** @} */
