<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Language file for local_mass_enroll, DE
 *
 * File         local_mass_enroll.php
 * Encoding     UTF-8
 *
 * @package     local_mass_enroll
 *
 * @copyright   1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @copyright   2012 onwards Patrick Pollet
 * @copyright   2015 onwards R.J. van Dongen
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['creategroupings'] = 'Bei Bedarf Gruppierung(en) erstellen';
$string['creategroups'] = 'Bei Bedarf Gruppe(n) erstellen';
$string['email_sent'] = 'Email geschickt an {$a}';
$string['enroll'] = 'In meinen Kurs einschreiben';
$string['firstcolumn'] = 'Erste Spalte enth&auml;lt';
$string['idnumber'] = 'ID-Nummer';
$string['im:already_in'] = '{$a} bereits eingeschrieben ';
$string['im:already_in_g'] = ' bereits in Gruppe {$a}';
$string['im:and_added_g'] = ' und zur Moodle-Gruppe {$a} hinzugef&uuml;gt';
$string['im:enrolled_ok'] = '{$a} eingeschrieben ';
$string['im:err_opening_file'] = 'Fehler beim &Ouml;ffnen von Datei {$a}';
$string['im:error_add_g_grp'] = 'Fehler beim Hinzuf&uuml;gen von Gruppe {$a->group} zu Gruppierung {$a->grouping}';
$string['im:error_add_grp'] = 'Fehler beim Hinzuf&uuml;gen von Gruppierung {$a->group} zu Kurs {$a->courseid}';
$string['im:error_addg'] = 'Fehler beim Hinzuf&uuml;gen von Gruppe {$a->group} zu Kurs {$a->courseid} ';
$string['im:error_adding_u_g'] = 'Fehler beim Hinzuf&uuml;gen zu Gruppe {$a}';
$string['im:error_g_unknown'] = 'Fehler - unbekannte Gruppe {$a} ';
$string['im:error_in'] = 'Fehler bei Einschreibung von {$a}';
$string['im:error_out'] = 'Fehler beim austragen von {$a}';
$string['im:not_in'] = '{$a} NICHT eingeschrieben ';
$string['im:opening_file'] = '&Ouml;ffne Datei: {$a} ';
$string['im:stats_g'] = '{$a->nb} Gruppe(n) erstellt: {$a->what}';
$string['im:stats_grp'] = '{$a->nb} Gruppierungen erstellt: {$a->what}';
$string['im:stats_i'] = '{$a} eingeschrieben';
$string['im:stats_ui'] = '{$a} ausgetragen';
$string['im:unenrolled_ok'] = '{$a} ausgetragen ';
$string['im:user_unknown'] = '{$a} unbekannt - Überspringe Zeile';
$string['im:using_role'] = 'Nutzer/in eingeschrieben als: {$a} ';
$string['mail_enrolment'] = '
Guten Tag,
Sie haben gerade folgende Nutzerinnen und Nutzer in Ihren Kurs \'{$a->course}\' eingeschrieben.
Hier ist ein Bericht der Aktionen :
{$a->report}
Mit freundlichen Grüßen.
';
$string['mail_enrolment_subject'] = 'Massen-Einschreibung in {$a}';
$string['mail_unenrolment'] = '
Guten Tag,
Sie haben gerade folgende Nutzerinnen und Nutzer aus Ihrem Kurs \'{$a->course}\' ausgetragen.
Hier ist ein Bericht der Aktionen :
{$a->report}
Mit freundlichen Grüßen.
';
$string['mail_unenrolment_subject'] = 'Massen-Austragung in {$a}';
$string['mailreport'] = 'Mail-Report schicken';
$string['mass_enroll'] = 'Massen-Einschreibung';
$string['mass_enroll:enrol'] = 'Einschreiben von Nutzer/innen in einen Kurs per CSV-Datei';
$string['mass_enroll:unenrol'] = 'Austragen von Nutzer/innen aus einem Kurs per CSV-Datei';
$string['mass_enroll_help'] = '
<h1>Masseneinschreibung</h1>

<p>Mit dieser Option schreiben Sie eine Liste mit bekannten Nutzerinnen und Nutzern ein. Verwenden Sie dazu eine Datei mit einem Nutzerkonto pro Zeile.</p>
<p><strong>Die erste Zeile,</strong> leere Zeilen oder unbekannte Konten werden &uuml;bersprungen. </p>
<p>Die Datei darf eine oder zwei Spalten enthalten, die durch ein Komma, Semikolon oder Tab voneinander getrennt sind.<br/>
Sie sollten die Datei mit einer Tabellenkalkulation auf Grundlage offizieller Teilnehmendenlisten erstellen und dann bei Bedarf eine Spalte mit den Gruppen hinzuf&uuml;gen, zu denen die Teilnehmenden zugewiesen werden sollen. Speichern Sie die Datei abschlie�end als <abbr title"Comma Separated Value">CSV</abbr>.(*)</p>

<p><strong>Die erste Spalte muss eine eindeutige Kontenbezeichnung enthalten: ID-Nummer (voreingestellt), Anmeldename oder Email </strong> des einzutragenden Nutzerkontos.(**)</p>
<p>Die zweite Spalte,<strong>sofern vorhanden,</strong> enth&auml;lt den Gruppennamen der Gruppe, zu der das Nutzerkonto hinzugef&uuml;gt werden soll.</p>

<p>Wenn die Gruppe nicht existiert, wird sie in dem Kurs erstellt, gemeinsam mit einer Gruppierung mit demselben Namen, zu dem die Gruppe hinzugef&uuml;gt wird.<br/>
Das geschieht deshalb, weil Moodle Aktivit&auml;ten auf Gruppierungen (Gruppen von Gruppen) beschr&auml;nkt werden k&ouml;nnen, nicht aber auf Gruppen. Deshalb wird dieses Vorgehen Ihr Leben leichter machen. Allerdings m&uuml;ssen Gruppierungen vom Admin freigegeben sein.</p>

<p>In der gleichen CSV-Datei k&ouml;nnen verschiedene Gruppen verwendet werden oder auch Nutzerkonten ohne Gruppenzuweisung genutzt werden.</p>

<p>Sie k&ouml;nnen die entsprechenden Optionen auch deaktivieren, wenn Sie sicher sind, dass die Gruppen und Gruppierungen schon vorhanden sind.</p>

<p>�blicherweise werden die Nutzer/innen als Studierende eingeschrieben, aber Sie k&ouml;nnen auch andere Rollen ausw&auml;hlen, wenn Sie die daf&uuml;r erforderlichen Rechte besitzen.</p>

<p>Diese Aktion kann beliebig oft wiederholt werden, wenn beispielsweise der Gruppenname vergessen oder falsch angegeben wurde.</p>

<h2>Beispieldatei</h2>

<p>ID-Nummern und eine Gruppen, die bei Bedarf im Kurs erstellt wird(*)</p>
<pre>
"idnumber";"group"
" 2513110";" 4GEN"
" 2512334";" 4GEN"
" 2314149";" 4GEN"
" 2514854";" 4GEN"
" 2734431";" 4GEN"
" 2514934";" 4GEN"
" 2631955";" 4GEN"
" 2512459";" 4GEN"
" 2510841";" 4GEN"
</pre>

<p>Nur ID-Nummern (**)</p>
<pre>
idnumber
2513110
2512334
2314149
2514854
2734431
2514934
2631955
</pre>

<p>Nur Email-Adressen(**)</p>
<pre>
email
toto@insa-lyon.fr
titi@]insa-lyon.fr
tutu@insa-lyon.fr
</pre>

<p>Kontobezeichnungen und Gruppen, durch Tabulatoren getrennt:</p>

<pre>
username	 group
ppollet      groupe_de_test              wird in die Gruppe eingetragen
codet        groupe_de_test              ebenso
astorck      autre_groupe                wird in eine andere Gruppe eingetragen
yjayet                                   keine Gruppe f&uuml;r dieses Konto
                                         leere Zeile wird &uuml;bersprungen
unknown                                  unbekanntes Konto wird ignoriert
</pre>

<p><span <font color=\'red\'>(*) </font></span>: doppelte Anf&uuml;hrungszeichen und Leerzeichen werden entfernt.</p>

<p><span <font color=\'red\'>(**) </font></span>: Die Nutzerkonten m&uuml;ssen in Moodle vorhanden sein. Das ist normalerweise der Fall, wenn Moodle mit einem externen Verzeichnis (LDAP, ...) synchronisiert wird.</p>


';
$string['mass_enroll_info'] = '
<p>Mit dieser Option schreiben Sie eine Liste mit bekannten Nutzer/innen ein. Verwenden Sie dazu eine Datei mit einem Nutzerkonto pro Zeile.</p>
<p><strong>Die erste Zeile,</strong> leere Zeilen oder unbekannte Konten werden &uuml;bersprungen. </p>
<p>Die Datei darf eine oder zwei Spalten enthalten, die durch ein Komma, Semikolon oder Tab voneinander getrennt sind.<br/>
<strong>Die erste Spalte muss eine eindeutige Kontenbezeichnung enthalten: ID-Nummer (voreingestellt), Anmeldename oder Email </strong> des einzutragenden Nutzerkontos.</p>
<p>Die zweite Spalte,<strong>sofern vorhanden,</strong> enth&auml;lt den Gruppennamen der Gruppe, zu der das Nutzerkonto hinzugef&uuml;gt werden soll.</p>
<p>Diese Aktion kann beliebig oft wiederholt werden, wenn beispielsweise der Gruppenname vergessen oder falsch angegeben wurde.</p>
';
$string['mass_unenroll'] = 'Massen-Austragung';
$string['mass_unenroll_help'] = '
<h1>Massen-Austragung</h1>

<p>Mit dieser Option tragen Sie eine Liste mit bekannten Nutzer/innen aus Ihrem Kurs aus. Verwenden Sie dazu eine Datei mit einem Nutzerkonto pro Zeile.</p>
<p><strong>Die erste Zeile,</strong> leere Zeilen oder unbekannte Konten werden &uuml;bersprungen.</p>
<p>Die Datei darf eine oder zwei Spalten enthalten, die durch ein Komma, Semikolon oder Tab voneinander getrennt sind.<br/>
Sie sollten die Datei mit einer Tabellenkalkulation auf Grundlage offizieller Teilnehmendenlisten erstellen und dann bei Bedarf eine Spalte mit den Gruppen hinzuf&uuml;gen, zu denen die Teilnehmenden zugewiesen werden sollen. Speichern Sie die Datei abschlie�end als <abbr title"Comma Separated Value">CSV</abbr>.(*)</p>

<p><strong>Die erste Spalte muss eine eindeutige Kontenbezeichnung enthalten: ID-Nummer (voreingestellt), Anmeldename oder Email </strong> des einzutragenden Nutzerkontos.(**)</p>
<p>Die zweite Spalte,<strong>sofern vorhanden,</strong> wird ignoriert.</p>

<p>Diese Aktion kann beliebig oft wiederholt werden, wenn beispielsweise der Gruppenname vergessen oder falsch angegeben wurde.</p>
<p><span <font color=\'red\'>(*) </font></span>: doppelte Anf&uuml;hrungszeichen und Leerzeichen werden entfernt.</p>

<p><span <font color=\'red\'>(**) </font></span>: Die Nutzerkonten m&uuml;ssen in Moodle vorhanden sein. Das ist normalerweise der Fall, wenn Moodle mit einem externen Verzeichnis (LDAP, ...) synchronisiert wird.</p>

';
$string['mass_unenroll_info'] = '
<p>Mit dieser Option tragen Sie eine Liste mit bekannten Nutzer/innen aus. Verwenden Sie dazu eine Datei mit einem Nutzerkonto pro Zeile.</p>
<p><strong>Die erste Zeile,</strong> leere Zeilen oder unbekannte Konten werden &uuml;bersprungen. </p>
<p>Die Datei darf eine oder zwei Spalten enthalten, die durch ein Komma, Semikolon oder Tab voneinander getrennt sind.
<strong>Die erste Spalte muss eine eindeutige Kontenbezeichnung enthalten: ID-Nummer (voreingestellt), Anmeldename oder Email </strong> des auszutragenden Nutzerkontos.</p>
<p>Andere Spalten, sofern vorhanden, werden ignoriert. Daher kann die zur Einschreibung genutzte CSV-Datei auch f&uuml;r die Austragung verwendet werden.
</p>
';
$string['pluginname'] = 'Mass enrolments';
$string['roleassign'] = 'Zuzuweisende Rolle';
$string['unenroll'] = 'Aus meinem Kurs austragen';
$string['username'] = 'Login';
