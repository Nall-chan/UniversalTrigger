<?php

declare(strict_types=1);

eval('namespace UniTrigger {?>' . file_get_contents(__DIR__ . '/helper/BufferHelper.php') . '}');
eval('namespace UniTrigger {?>' . file_get_contents(__DIR__ . '/helper/DebugHelper.php') . '}');

/*
 * @addtogroup unitrigger
 * @{
 *
 * @package       UniTrigger
 * @file          UniversalTriggerBase.php
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2019 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       1.71
 *
 */

/**
 * UniversalTriggerBase Basis-Klasse für die die Überwachung von Variablen auf fehlende Änderung/Aktualisierung.
 * Erweitert IPSModule.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2019 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 * @version       1.71
 *
 * @example <b>Ohne</b>
 *
 * @method bool SendDebug(string $Message, mixed $Data, int $Format)
 */
class UniversalTriggerBase extends IPSModuleStrict
{
    use \UniTrigger\DebugHelper;
    use \UniTrigger\BufferHelper;

    public static $Messages = [
        IPS_OBJECTMESSAGE + 3   => 'Object has new parent',
        IPS_OBJECTMESSAGE + 4   => 'Object change name',
        IPS_OBJECTMESSAGE + 5   => 'Object change info',
        IPS_OBJECTMESSAGE + 7   => 'Object change summary',
        IPS_OBJECTMESSAGE + 8   => 'Object change position',
        IPS_OBJECTMESSAGE + 10  => 'Object change hidden',
        IPS_OBJECTMESSAGE + 11  => 'Object change icon',
        IPS_OBJECTMESSAGE + 12  => 'Object has new child',
        IPS_OBJECTMESSAGE + 13  => 'Object child removed',
        IPS_OBJECTMESSAGE + 14  => 'Object change ident',
        IPS_VARIABLEMESSAGE + 3 => 'Variable update',
        IPS_VARIABLEMESSAGE + 4 => 'Variable change profil',
        IPS_VARIABLEMESSAGE + 5 => 'Variable change action',
        IPS_SCRIPTMESSAGE + 4   => 'Script broken',
        IPS_INSTANCEMESSAGE + 3 => 'Instance connected',
        IPS_INSTANCEMESSAGE + 4 => 'Instance disconnected',
        IPS_INSTANCEMESSAGE + 5 => 'Instance change status',
        IPS_INSTANCEMESSAGE + 6 => 'Instance change settings',
        IPS_EVENTMESSAGE + 3    => 'Event updated',
        IPS_EVENTMESSAGE + 4    => 'Event change active',
        IPS_EVENTMESSAGE + 5    => 'Event change limits',
        IPS_EVENTMESSAGE + 6    => 'Event change script',
        IPS_EVENTMESSAGE + 7    => 'Event change trigger',
        IPS_EVENTMESSAGE + 8    => 'Event change triggervalue',
        IPS_EVENTMESSAGE + 9    => 'Event change triggerexecution',
        IPS_EVENTMESSAGE + 10   => 'Event change cyclic',
        IPS_EVENTMESSAGE + 11   => 'Event change date from',
        IPS_EVENTMESSAGE + 12   => 'Event change date to',
        IPS_EVENTMESSAGE + 13   => 'Event change time from',
        IPS_EVENTMESSAGE + 14   => 'Event change time to',
        IPS_MEDIAMESSAGE + 3    => 'Media change file',
        IPS_MEDIAMESSAGE + 4    => 'Media available',
        IPS_MEDIAMESSAGE + 5    => 'Media updated',
        IPS_LINKMESSAGE + 3     => 'Link change target',
        IPS_ENGINEMESSAGE + 2   => 'Script executed',
        IPS_ENGINEMESSAGE + 3   => 'Script running'
    ];
    /**
     * Interne Funktion des SDK.
     */
    public function MessageSink(int $TimeStamp, int $SenderID, int $Message, array $Data): void
    {
        $this->FireTargetScript($TimeStamp, $SenderID, $Message, $Data);
    }
    /**
     * Startet das Ziel-Skript.
     *
     * @param int   $TimeStamp
     * @param int   $SenderID
     * @param int   $Message
     * @param mixed $Data
     */
    protected function FireTargetScript(int $TimeStamp, int $SenderID, int $Message, mixed $Data): void
    {
        $ScriptID = $this->ReadPropertyInteger('ScriptID');
        if ($ScriptID == 0) {
            return;
        }
        if ($SenderID == $ScriptID) {
            return;
        }

        if (IPS_ScriptExists($ScriptID)) {
            IPS_RunScriptEx($ScriptID, [
                'EVENT'     => $SenderID,
                'VALUE'     => $Message,
                'TIMESTAMP' => $TimeStamp,
                'DATA'      => json_encode($Data),
                'INSTANCE'  => $this->InstanceID,
                'SENDER'    => 'UniTrigger']);
        } else {
            $this->LogMessage(sprintf($this->Translate('Script %d not exists'), $this->ReadPropertyInteger('ScriptID')), KL_WARNING);
        }
    }

    /**
     * Deregistriert eine Überwachung eines Objektes.
     *
     * @param int $ObjektID  IPS-ID
     * @param int $MessageID
     */
    protected function UnregisterMessage(int $ObjektID, int $MessageID): bool
    {
        if ($ObjektID == 1) {
            return false;
        }
        if ($MessageID == 0) {
            return false;
        }
        if (!IPS_ObjectExists($ObjektID)) {
            return false;
        }
        $this->SendDebug('UnRegisterWatch:' . $ObjektID, $MessageID, 0);
        return parent::UnregisterMessage($ObjektID, $MessageID);
    }

    /**
     * Registriert eine Überwachung eines Objektes.
     *
     * @param int $ObjektID  IPS-ID
     * @param int $MessageID
     */
    protected function RegisterMessage(int $ObjektID, int $MessageID): bool
    {
        if ($ObjektID == 1) {
            return false;
        }
        if ($MessageID == 0) {
            return false;
        }
        if (!IPS_ObjectExists($ObjektID)) {
            return false;
        }
        $this->SendDebug('RegisterWatch:' . $ObjektID, $MessageID, 0);
        return parent::RegisterMessage($ObjektID, $MessageID);
    }
}

/* @} */
