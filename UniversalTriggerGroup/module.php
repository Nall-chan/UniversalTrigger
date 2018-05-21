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
 * @version       1.1
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
 * @version       1.1
 * @example <b>Ohne</b>
 *
 * @property array $OldTrigger
 */
class UniversalTriggerGroup extends UniversalTriggerBase
{

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Create()
    {
        parent::Create();
        $this->OldTrigger = [];
        $this->RegisterPropertyInteger('ScriptID', 0);
        $this->RegisterPropertyString('Trigger', json_encode([]));
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
        foreach ($this->OldTrigger as $ObjektTrigger) {
            $this->UnregisterMessage($ObjektTrigger->ObjectId, $ObjektTrigger->MessageId);
        }
        parent::ApplyChanges();
        $NewTrigger = json_decode($this->ReadPropertyString('Trigger'));
        foreach ($NewTrigger as $ObjektTrigger) {
            $this->RegisterMessage($ObjektTrigger->ObjectId, $ObjektTrigger->MessageId);
        }
        $this->OldTrigger = $NewTrigger;
    }

    public function GetConfigurationForm()
    {
        $form = json_decode(file_get_contents(__DIR__ . "/form.json"), true);
        $Triggers = json_decode($this->ReadPropertyString('Trigger'), true);
        foreach ($Triggers as &$Trigger) {
            if (!IPS_ObjectExists($Trigger['ObjectId']) or ($Trigger['ObjectId'] == 0)) {
                $Trigger['Name'] = sprintf($this->Translate("Object #%d not exists"), $Trigger['ObjectId']);
            } else {
                $Trigger['Name'] = IPS_GetLocation($Trigger['ObjectId']);
            }
        }
        $form['elements'][1]['values'] = $Triggers;
        foreach (self::$Messages as $MessageId => $MessageName) {
            $Messages[] = array('label' => $MessageName, 'value' => $MessageId);
        }
        $form['elements'][1]['columns'][1]['edit']['options'] = $Messages;
        return json_encode($form);
    }

    ################## PRIVATE
}

/** @} */
