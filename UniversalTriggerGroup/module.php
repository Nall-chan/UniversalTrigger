<?php

declare(strict_types=1);
/*
 * @addtogroup unitrigger
 * @{
 *
 * @package       UniTrigger
 * @file          module.php
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2020 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       1.6
 *
 */

require_once __DIR__ . '/../libs/UniversalTriggerBase.php';

/**
 * UniversalTriggerGroup Klasse für die Nutzung der IPS Nachrichten in einem PHP-Script.
 * Erweitert UniversalTriggerBase.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2020 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 * @version       1.6
 *
 * @example <b>Ohne</b>
 *
 * @property array $OldTrigger
 */
class UniversalTriggerGroup extends UniversalTriggerBase
{
    /**
     * Interne Funktion des SDK.
     */
    public function Create()
    {
        parent::Create();
        $this->OldTrigger = [];
        $this->RegisterPropertyInteger('ScriptID', 0);
        $this->RegisterPropertyString('Trigger', json_encode([]));
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
        foreach ($this->OldTrigger as $ObjektTrigger) {
            $this->UnregisterMessage($ObjektTrigger->ObjectId, $ObjektTrigger->MessageId);
            $this->UnregisterReference($ObjektTrigger->ObjectId);
        }
        parent::ApplyChanges();
        $NewTrigger = json_decode($this->ReadPropertyString('Trigger'));
        foreach ($NewTrigger as $ObjektTrigger) {
            $this->RegisterMessage($ObjektTrigger->ObjectId, $ObjektTrigger->MessageId);
            $this->RegisterReference($ObjektTrigger->ObjectId);
        }
        $this->OldTrigger = $NewTrigger;
    }

    public function GetConfigurationForm()
    {
        $form = json_decode(file_get_contents(__DIR__ . '/form.json'), true);
        $Triggers = json_decode($this->ReadPropertyString('Trigger'), true);
        $form['elements'][1]['values'] = $Triggers;
        foreach (self::$Messages as $MessageId => $MessageName) {
            $Messages[] = ['label' => $MessageName, 'value' => $MessageId];
        }
        $form['elements'][1]['columns'][1]['edit']['options'] = $Messages;
        return json_encode($form);
    }

    //################# PRIVATE
}

/* @} */
