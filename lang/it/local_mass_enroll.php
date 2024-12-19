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
 * Language file for local_mass_enroll, IT
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
$string['creategroupings'] = 'Crea i raggruppamenti se necessario';
$string['creategroups'] = 'Crea i gruppi se necessario';
$string['email_sent'] = 'email spedita a {$a}';
$string['enablemassenrol'] = 'Permetti l\'iscrizione massiva dall\'amministrazione del corso';
$string['enablemassenrol_help'] = 'Marca quest\'opzione per abilitare l\'iscrizione massiva nel menu di amministrazione del corso';
$string['enablemassunenrol'] = 'Permetti la disiscrizione massiva dall\'amministrazione del corso';
$string['enablemassunenrol_help'] = 'Marca quest\'opzione per abilitare la disiscrizione massiva nel menu di amministrazione del corso';
$string['enroll'] = 'Iscrivili al mio corso';
$string['firstcolumn'] = 'La prima colonna contiene';
$string['idnumber'] = 'Codice identificativo';
$string['im:already_in'] = '{$a} già iscritto ';
$string['im:already_in_g'] = ' già nel gruppo {$a}';
$string['im:and_added_g'] = ' e aggiunto al gruppo Moodle {$a}';
$string['im:enrolled_ok'] = '{$a} iscritto ';
$string['im:err_opening_file'] = 'errore di accesso al file {$a}';
$string['im:error_add_g_grp'] = 'errore nell\'aggiungere il gruppo {$a->group} al raggruppamento {$a->grouping}';
$string['im:error_add_grp'] = 'errore nell\'aggiungere il raggruppamento {$a->group} al corso {$a->courseid}';
$string['im:error_addg'] = 'errore nell\'aggiungere il gruppo {$a->group} al corso {$a->courseid} ';
$string['im:error_adding_u_g'] = 'impossibile aggiungere al gruppo {$a}';
$string['im:error_g_unknown'] = 'errore, gruppo {$a} sconosciuto';
$string['im:error_in'] = 'errore nell\'iscrivere {$a} ';
$string['im:error_out'] = 'errore nel disiscrivere {$a}';
$string['im:not_in'] = '{$a} non iscritto ';
$string['im:stats_g'] = '{$a->nb} gruppi creati: {$a->what}';
$string['im:stats_grp'] = '{$a->nb} raggruppamenti creati: {$a->what}';
$string['im:stats_i'] = '{$a} iscritti';
$string['im:stats_ui'] = '{$a} disiscritti';
$string['im:unenrolled_ok'] = '{$a} disiscritto ';
$string['im:user_unknown'] = '{$a} sconosciuto - riga ignorata ';
$string['im:using_role'] = 'Utenti iscritti come: {$a} ';
$string['localmassenrolldefaults'] = 'Impostazioni di default iscrizione massiva';
$string['localmassenrollextensions'] = 'Impostazioni dell\'estensione del menu';
$string['mail_enrolment'] = '
Salve,
  hai appena iscritto i seguenti utenti al tuo corso \'{$a->course}\'.
Ecco il rapporto delle operazioni:
{$a->report}
Buon lavoro.
';
$string['mail_enrolment_subject'] = 'Iscrizione massiva a {$a}';
$string['mail_unenrolment'] = '
Salve,
  hai appena disiscritto i seguenti utenti al tuo corso \'{$a->course}\'.
Ecco il rapporto delle operazioni:
{$a->report}
Buon lavoro.
';
$string['mail_unenrolment_subject'] = 'Disiscrizione massiva da {$a}';
$string['mailreport'] = 'Inviami un report per email';
$string['mailreportdefault'] = 'Default per l\'invio di report';
$string['mailreportdefault_help'] = 'Configura il default per l\'invio dei report di iscrizione/disiscrizione massiva';
$string['mass_enroll'] = 'Iscrizione massiva';
$string['mass_enroll:enrol'] = 'Iscrivere gli utenti ad un corso con un file CSV';
$string['mass_enroll:unenrol'] = 'Disiscrivere gli utenti da un corso con un file CSV';
$string['mass_enroll_help'] = '

<h1>Iscrizioni massive</h1>

<p>
Con quest\'opzione puoi disiscrivere massivamente dal tuo corso una lista di utenti iscritti al corso,
elencati in un file che hai preparato, uno per riga
</p>
<p>
<b>La prima riga</b>, righe vuote, e le righe con un identificativo sconosciuto saranno ignorate.
</p>

<p>
Il file può contenere più colonne, separate da virgola, punto e virgola o un tabulatore. <br/>
Può essere preparato con un qualsiasi foglio elettronico a partire dai dati delle iscrizioni,
ad esempio, esportandoli in CSV (Testo separato da virgole) (*)</p>

<p>
<b>La prima deve contenete un identificatore univoco dell\'utente interessato,
per default il codice identificativo/maricola (idnumber Moodle), ma può anche essere
il nome utente (username Moodle) o indirizzo email (**). </p>

<p>
La seconda, <b>se presente,</b> indica il gruppo (all\'interno di questo corso Moodle) in cui iscrivere l\'utente.<br/>
Se il gruppo non esiste già nel vostro corso, sarà creato automaticamente, così come il corrispondente raggruppamento omonimo.<br/>
Questo perché in Moodle le attività possono essere ristrette ai ragguppamenti (gruppi di gruppi) e non ai gruppi,
semplificandoti le attività. I raggruppamenti devono essere abilitati dall\'amministrazione del sito.<br/>

Nel file possono essere indicati gruppi differenti (o nessun gruppo) in ogni riga.<br/>

Altre colonne verranno ignorate.<br/>

Potete disattivare le opzioni per la creazione automatica dei gruppi e dei raggruppamenti se sono già definiti;
</p>

<p>
Per default ogni utente viene iscritto come studente, ma puoi specificare un altro ruolo che sei abilitato ad assegnare.
</p>

<p>
L\'operazione può essere ripetuta più volte senza danni, per esempio se avete dimenticato il gruppo o qualche utente
oppure per correggere un errore ortografico.
</p>

<h2>Esempio di file</h2>

matricola studente e gruppo da creare se necessario (*)
<pre>
"matricola";"gruppo" <b>la prima riga verrà ignorata!</b>
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

solo la matricola studente (**)
<pre>
numéro INSA
2513110
2512334
2314149
2514854
2734431
2514934
2631955
</pre>

solo l\'indirizzo email (**)
<pre>
email
toto@insa-lyon.fr
titi@]insa-lyon.fr
tutu@insa-lyon.fr
</pre>

username e il gruppo (separato da tabulatore):
<pre>
nome utente  gruppo
ppollet      groupe_de_test              sarà in questo gruppo
codet        groupe_de_test              anche lui
astorck      autre_groupe                e lui in un altro gruppo
yjayet                                  in nessun gruppo
                                        riga vuota ignorata
sconosciuto                             utente ignorato
</pre>

<p>
<span <font color=\'red\'>(*) </font></span>: virgolette e spazi aggiunti da alcuni fogli elettronici vengono rimossi.
</p>

<p>
<span <font color=\'red\'>(**) </font></span>: gli utenti da iscrivere devono essere già registrati in Moodle;
questa situazione è comune se Moodle è sincronizzato con qualche sorgente esterna (LDAP, ecc...).
</p>
';
$string['mass_enroll_info'] = '
<p>
Con quest\'opzione puoi iscrivere massivamente al tuo corso una lista di utenti già registrati in Moodle
elencati in un file che hai preparato, uno per riga
</p>
<p>
<b>La prima riga</b>, righe vuote, e le righe con un identificativo sconosciuto saranno ignorate.
</p>
<p>
Il file può contenere uno o due colonne, separate da virgola, punto e virgola o un tabulatore. <br/>
<b>La prima deve contenete un identificatore univoco: codice identificativo (idnumber Moodle), username o indirizzo email dell\'utente. <br/>
La seconda, <b>se presente,</b> indica il gruppo (all\'interno di questo corso Moodle) in cui iscrivere l\'utente.<br/>
Altre colonne verranno ignorate.<br/>
L\'operazione può essere ripetuta più volte senza danni, per esempio se avete dimenticato il gruppo o qualche utente.
</p>
';
$string['mass_unenroll'] = 'Disiscrizione massiva';
$string['mass_unenroll_help'] = '
<h1>Disiscrizione massiva</h1>

<p>
Con quest\'opzione puoi disiscrivere massivamente dal tuo corso una lista di utenti iscritti al corso,
elencati in un file che hai preparato, uno per riga
</p>

<p>
<b>La prima riga</b>, righe vuote, e le righe con un identificativo sconosciuto saranno ignorate.
</p>
<p>
Il file può contenere uno o più colonne, separate da virgola, punto e virgola o un tabulatore. <br/>
Può essere preparato con un qualsiasi foglio elettronico a partire dai dati delle iscrizioni,
ad esempio, esportandoli in CSV (Testo separato da virgole), oppure
essere lo stesso usato per l\'iscrizione massiva. (*)</p>

<p>
<b>La prima deve contenete un identificatore univoco: codice identificativo
(idnumber Moodle), username o indirizzo email dell\'utente. (**)<br/>

<p>
e altre colonne verranno ignorate.</p>


<p>
L\'operazione può essere ripetuta più volte senza danni, per esempio se avete dimenticato qualche utente.
</p>

<p>
<span <font color=\'red\'>(*) </font></span>: virgolette e spazi aggiunti da alcuni fogli elettronici vengono rimossi.
</p>

<p>
<span <font color=\'red\'>(**) </font></span>: gli utenti da iscrivere devono essere già registrati in Moodle;
questa situazione è comune se Moodle è sincronizzato con qualche sorgente esterna (LDAP, ecc...).
</p>
';
$string['mass_unenroll_info'] = '
<p>
Con quest\'opzione puoi disiscrivere massivamente dal tuo corso una lista di utenti iscritti al corso,
elencati in un file che hai preparato, uno per riga
</p>
<p>
<b>La prima riga</b>, righe vuote, e le righe con un identificativo sconosciuto saranno ignorate.
</p>
<p>
Il file può contenere uno o più colonne, separate da virgola, punto e virgola o un tabulatore. <br/>
<b>La prima deve contenete un identificatore univoco: codice identificativo (idnumber Moodle), username o indirizzo email dell\'utente. <br/>
Le altre colonne verranno ignorate. Il file può essere lo stesso utilizzato per l\'operazione di iscrizione massiva.<br/>
L\'operazione può essere ripetuta più volte senza danni, per esempio se avete dimenticato qualche utente.
</p>
';
$string['massenrollsettings'] = 'Impostazioni iscrizione massiva';
$string['pluginname'] = 'Iscrizione massiva';
$string['roleassign'] = 'Iscrivi come';
$string['unenroll'] = 'Disiscrivili dal mio corso';
$string['username'] = 'username';
