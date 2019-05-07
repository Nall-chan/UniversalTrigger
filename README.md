[![SDK](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Modul%20Version-1.5-blue.svg)]()
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)  
[![Version](https://img.shields.io/badge/Symcon%20Version-5.1%20%3E-green.svg)](https://www.symcon.de/forum/threads/30857-IP-Symcon-5-1-%28Stable%29-Changelog)
[![StyleCI](https://styleci.io/repos/134292227/shield?style=flat)](https://styleci.io/repos/134292227)  

# Symcon-Modul: UniversalTrigger

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang) 
2. [Voraussetzungen](#2-voraussetzungen)
3. [Installation](#3-installation)
4. [Universaltrigger (single)](#4-universaltrigger-single)
5. [Universaltrigger (group)](#5-universaltrigger-group)
6. [Variablen im Ziel-Script](#6-variablen-im-ziel-script)
7. [Parameter / Modul-Infos](#7-parameter--modul-infos)
8. [Anhang](#8-anhang)
9. [Spenden](#9-spenden)
10. [Lizenz](#10-lizenz)

## 1. Funktionsumfang

Ermöglicht es auch Änderungen in IP-Symcon zu reagieren, welche nicht über normale Ereignisse oder das Event-Control abgebildet werden.  

So können mit den enthaltenden Modulen PHP-Scripte gestartet werden wenn z.B:

* Ein Objekt verschoben wurde
* Der Name des Objektes verändert wurde
* Die Sichtbarkeit eines Objektes sich geändert hat
* Die Einstellungen einer Instanz verändert wurden
* Ein Ereignis de- oder aktiviert wurde
* Ein Mediaobjekt aktualisert wurde
* Ein Link sich geändert hat

u.v.m.

## 2. Voraussetzungen

 - IPS ab Version 5.1
 
## 3. Installation

### IPS 5.1:
   Bei privater Nutzung:
     Über den 'Module-Store' in IPS.  
   **Bei kommerzieller Nutzung (z.B. als Errichter oder Integrator) wenden Sie sich bitte an den Autor.**  

## 4. Universaltrigger (single)

 Unter Instanz hinzufügen ist der 'Universaltrigger (single') unter den Kerninstanzen '(Kern)' zu finden.  
 Jeweils einmal als Typ Single und Group.  
        
 Nach dem Anlegen der Instanz ist diese noch entsprechend zu konfigurieren.  
        
 - Script:  
    Ziel-Script welches ausgeführt wird, wenn der zu Überwachende Zustand eintritt.  
        
 - Objekt:  
    Ein beliebiges Objekt (außer 0) welche überwacht werden soll.  
        
 - Nachricht:  
    Die Nachricht auf welche reagiert werden soll, wenn sie beim Objekt eintritt.  

**Hinweis**:  
Es wird nicht validiert ob die Einstellungen sinnvoll bzw. überhaupt möglich sind.  
So kann als Objekt eine Kategorie ausgewählt werden, und als Nachricht 'Script defekt'.  
Dieser Zustand ist bei einer Kategorie aber niemals gegeben.  
Somit wird das Ziel-Script auch nie gestartet.  

## 5. Universaltrigger (group)

 Die Konfiguration und die Funktion sind  nahezu identisch zu der Variante 'Single'.  
 Hier werden die Objekte und Nachrichten in einer Liste eingetragen.  
 So können z.B. für das gleiche Objekt mehrere Nachrichten, oder für die gleichen Nachrichten mehrere Objekte unter einem Ziel-Skript zusammengefasst werden.  

## 6. Variablen im Ziel-Script

Anhand der PHP-Variable $_IPS ist es möglich im Ziel-Script auf alle Werte der Nachrichten und des Ereignissen zuzugreifen.   
Folgende Felder im Array der PHP-Variable $_IPS stehen im Ziel-Script zur Verfügung:    

| Index     | Typ     | Beschreibung                                                                |
| :--------:|:------: | :-------------------------------------------------------------------------: |
| SELF      | integer | Objekt ID des Skriptes                                                      |
| INSTANCE  | integer | Instanz ID des auslösenden Universaltrigger                                 |
| EVENT     | integer | Objekt ID von welchem die Nachricht stammt                                  |
| VALUE     | integer | Die Nachricht welche das Skript gestartet hat (siehe 1*)                    |
| DATA      | string  | JSON codiertes Array welches alle Daten der Nachticht enthält (siehe 2*)    |
| TIMESTAMP | integer | Zeitpunkt der Nachricht als UnixTimestamp                                   |
| SENDER    | string  | immer 'UniTrigger'                                                          |

 Das Ziel-Script kann anhand von 'EVENT' und 'VALUE' unterschiedliche Aktionen ausführen.  
 Dies ist gerade bei beim 'Universaltrigger (group)' sehr hilfreich.  

**Hinweis 1:**  
Die Übersetzung der Werte der Nachrichten sind hier zu finden:  
 [Nachrichten](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/nachrichten/)  
Beispiel:  
 Übergeordnetes Objekt hat sich geändert entspricht dem Wert 10403.  

**Hinweis 2:**  
Das Array kann aus $_IPS['DATA'] einfach mit json_decode decodiert werden:
```php
$Data = json_decode($_IPS['DATA'],true);
```
Der Inhalt von 'DATA' unterscheidet sich je nach Nachricht und ist *nicht* dokumentiert.  

**Hinweis 3:**  
Ausgaben des Ziel-Skript werden nur im Meldungsfenster bzw. LogFile ausgegeben, da das Script immer durch IPS gestartet wird und niemals durch die Console.  

**Hinweis 4:**  
Beispiel um festzustellen welche Werte in $_IPS und $_IPS['DATA'] enthalten sind:

```php
var_dump($_IPS); // Erstes Array unter Meldungen
$Data = json_decode($_IPS['DATA'],true);
var_dump($Data); // Zweites Array unter Meldungen
```

Ausgabe:  
```21.05.2018 17:39:07 | ScriptEngine | Ergebnis für Ereignis 23782
array(8) {
  ["SELF"]=>
  int(30118)
  ["EVENT"]=>
  int(23782)
  ["VALUE"]=>
  int(10804)
  ["INSTANCE"]=>
  int(35040)
  ["TIMESTAMP"]=>
  int(0)
  ["DATA"]=>
  string(7) "[false]"
  ["SENDER"]=>
  string(10) "UniTrigger"
  ["THREAD"]=>
  int(18)
}
array(1) {
  [0]=>
  bool(false)
}
```

## 7. Parameter / Modul-Infos

**GUIDs der Instanzen (z.B. wenn Instanz per PHP angelegt werden soll):**  

| Instanz                   | GUID                                   |
| :-----------------------: | :------------------------------------: |
| Universaltrigger (single) | {4FA5F724-D93B-457B-94EC-E80CFF5415D8} |
| Universaltrigger (group)  | {A79F745E-FFB8-4D69-BD25-6914AC5A50AE} |

**Eigenschaften von Universaltrigger (single):**  

| Eigenschaft   | Typ     | Standardwert | Funktion                                     |
| :-----------: | :-----: | :----------: | :------------------------------------------: |
| ScriptID      | integer | 0            | Ziel-Script                                  |
| ObjectId      | integer | 0            | Objekt ID welches überwacht wird             |
| MessageId     | integer | 10403        | Wert der Nachricht auch welche reagiert wird |

**Eigenschaften von Universaltrigger (group):**  

| Eigenschaft   | Typ     | Standardwert | Funktion                                                                  |
| :-----------: | :-----: | :----------: | :-----------------------------------------------------------------------: |
| ScriptID      | integer | 0            | Ziel-Script                                                               |
| Trigger       | string  | []           | Konfiguration von Objekten und Nachrichten als JSON codiertes Array       |

## 8. Anhang

**Changlog:**  

Version 1.5:  
 - Release für IPS 5.1 und den Module-Store  

Version 1.1:  
 - Erstes Release  

## 9. Spenden  
  
  Die Library ist für die nicht kommzerielle Nutzung kostenlos, Schenkungen als Unterstützung für den Autor werden hier akzeptiert:  

<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=G2SLW2MEMQZH2" target="_blank"><img src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" /></a>


## 10. Lizenz  

[CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)  
