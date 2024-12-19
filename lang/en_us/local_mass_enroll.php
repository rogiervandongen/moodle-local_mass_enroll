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
 * Language file for local_mass_enroll, EN-US
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
$string['allowedunenrolmentmethods'] = 'Allowed extra unenrolments';
$string['allowedunenrolmentmethods_help'] = 'Indicate the default allowed enrolment methods that can be used for mass unenrolment.<br/>
Please note "manual" cannot be configured as this is the default and will always be processed.<br/>
<i>Be careful which methods you select, because it might raise questions! For example, you might <strong>not</strong> want to remove enrolments that were paid for (paypal, stripe, classicpay, etc etc).</i>
';
$string['checknonmanualenrolments'] = 'Check other enrolments than manual?';
$string['checknonmanualenrolments_help'] = 'When enabled, role assignments from enrolment methods other than manual will be checked, meaning the target role can be applied from any enrolment plugin.
If the user has the target role from any enrolment plugin, processing will be skipped';
$string['creategroupings'] = 'Create  grouping(s) if needed';
$string['creategroups'] = 'Create group(s) if needed';
$string['defaultrole'] = 'Default role to assign';
$string['defaultrole_help'] = 'Provide the default role to assign when not present in the CSV';
$string['email_sent'] = 'email sent to {$a}';
$string['enableextraunenrolmentplugins'] = 'Allow extra unenrolment plugins?';
$string['enableextraunenrolmentplugins_help'] = 'Use this if you wish to allow for unenrolment of other plugins than "manual"';
$string['enablemassenrol'] = 'Allow mass enrolment from course administration';
$string['enablemassenrol_help'] = 'Check this option to allow extension for mass enrolment in the course administration tree';
$string['enablemassunenrol'] = 'Allow mass unenrolment from course administration';
$string['enablemassunenrol_help'] = 'Check this option to allow extension for mass unenrolment in the course administration tree';
$string['enclosure'] = 'CSV Enclosure';
$string['enroll'] = 'Enroll them to my course';
$string['firstcolumn'] = 'First column contains';
$string['idnumber'] = 'Id number';
$string['im:already_in'] = '{$a} already enrolled ';
$string['im:already_in_g'] = ' already in group {$a}';
$string['im:and_added_g'] = ' and added to Moodle\'s  group  {$a}';
$string['im:enrolled_ok'] = '{$a} enrolled ';
$string['im:err_opening_file'] = 'error opening file {$a}';
$string['im:error_add_g_grp'] = 'error adding group {$a->group} to grouping {$a->grouping}';
$string['im:error_add_grp'] = 'error adding grouping {$a->group} to course {$a->courseid}';
$string['im:error_addg'] = 'error adding group {$a->group}  to course {$a->courseid} ';
$string['im:error_adding_u_g'] = 'error adding to group  {$a}';
$string['im:error_g_unknown'] = 'error unkown group {$a} ';
$string['im:error_in'] = 'error enrolling {$a}';
$string['im:error_out'] = 'error unenrolling {$a}';
$string['im:not_in'] = '{$a} NOT enrolled ';
$string['im:opening_file'] = 'Opening file : {$a} ';
$string['im:stats_g'] = '{$a->nb} group(s) created : {$a->what}';
$string['im:stats_grp'] = '{$a->nb} grouping(s) created : {$a->what}';
$string['im:stats_i'] = '{$a} enrolled';
$string['im:stats_ui'] = '{$a} unenrolled';
$string['im:unenrolled_ok'] = '{$a} unenrolled ';
$string['im:user_unknown'] = '{$a} unknown - skipping line';
$string['im:using_role'] = 'Enrolling users as : {$a} ';
$string['localmassenrolldefaults'] = 'Mass enrol default settings';
$string['localmassenrollextensions'] = 'Menu extension settings';
$string['mail_enrolment'] = '
Hello,
You just enrolled the following list of users to your course \'{$a->course}\'.
Here is a report of operations :
{$a->report}
Sincerly.
';
$string['mail_enrolment_subject'] = 'Bulk enrollments on {$a}';
$string['mail_unenrolment'] = '
Hello,
You just unenrolled the following list of users to your course \'{$a->course}\'.
Here is a report of operations :
{$a->report}
Sincerly.
';
$string['mail_unenrolment_subject'] = 'Bulk unenrollments on {$a}';
$string['mailreport'] = 'Send me a mail report';
$string['mailreportdefault'] = 'Send reports default';
$string['mailreportdefault_help'] = 'Configure the default setting for sending reports for the bulk (un)enrolment forms';
$string['mapping:column'] = 'Column {$a}';
$string['mappings'] = 'Mappings';
$string['mass_enroll'] = 'Bulk enrollments';
$string['mass_enroll:enrol'] = 'Enroll users to a course by CSV file';
$string['mass_enroll:unenrol'] = 'Unenroll users from a course by CSV file';
$string['mass_enroll_help'] = '
<h1>Bulk enrollments</h1>

<p>
With this option you are going to enroll a list of known users from a file with one account per line
</p>
<p>
<b> The firstline </b> the empty lines or unknown accounts will be skipped. </p>

<p>
The file may contains one or two columns, separated by a comma, a semi-column or a tabulation.

You should prepare it from your usual spreadsheet program from official lists of students, for example,
and add if needed a column with groups to which you want these users to be added. Finally export it as CSV. (*)</p>

<p>
<b> The first one must contains a unique account identifier </b>: idnumber (by default) login or email  of the target user. (**). </p>

<p>
The second <b>if present,</b> contains the group name in wich you want that user to be added. </p>

<p>
If the group name does not exist, it will be created in your course, together with a grouping of the same name to which the group will be added.
.<br/>
This is due to the fact that in Moodle, activities can be restricted to groupings (group of groups), not groups,
 so it will make your life easier. (this requires that groupings are enabled by your site administrator).

<p>
You may have in the same file different target groups or no groups for some accounts
</p>

<p>
You may unselect options to create groups and groupings if you are sure that they already exist in the course.
</p>

<p>
By default the users will be enrolled as students but you may select other roles that you are allowed to manage (teacher, non editing teacher
or any custom roles)
</p>

<p>
You may repeat this operation at will without dammages, for example if you forgot or mispelled the target group.
</p>


<h2> Sample files </h2>

Id numbers and a group name to be created in needed in the course (*)
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

only idnumbers (**)
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

only emails (**)
<pre>
email
toto@insa-lyon.fr
titi@]insa-lyon.fr
tutu@insa-lyon.fr
</pre>

usernames and groups, separated by a tab :

<pre>
username   group
ppollet      groupe_de_test              will be in that group
codet        groupe_de_test              also him
astorck      autre_groupe                will be in another group
yjayet                                   no group for this one
                                         empty line skipped
unknown                                  unknown account skipped
</pre>

<p>
<span <font color=\'red\'>(*) </font></span>: double quotes and spaces, added by some spreadsheet programs will be removed.
</p>

<p>
<span <font color=\'red\'>(**) </font></span>: target account must exist in Moodle ; this is normally the case if Moodle is synchronized with
some external directory (LDAP...)
</p>
';
$string['mass_enroll_info'] = '
<p>With this option you are going to enrol a list of known users from a file with one account per line</p>
<p>Empty lines or unknown accounts will be skipped.</p>
<p>The file may contains several columns, separated by a comma, a semi-column or a tabulation.</p>
<p>Every relevant column <b>must</b> be mapped; see the "Mappings" section in the form below.</p>
<p>You may repeat this operation at will without damages, for example if you forgot the group for some users.</p>
';
$string['mass_unenroll'] = 'Bulk unenrollments';
$string['mass_unenroll_help'] = '
<h1>Bulk unenrollments</h1>

<p>
With this option you are going to unenroll a list of users from a file with one account per line
</p>
<p>
<b> The firstline </b> the empty lines or unknown accounts will be skipped. </p>

<p>
The file may contains several columns, separated by a comma, a semi-column or a tabulation.

You should prepare it from your usual spreadsheet program from an official lists of students, for example,
by exporting the course gradebook to CSV, or use the very same file as the one used to mass enroll users. (*)</p>

<p>
<b> The first one must contains a unique account identifier </b>: idnumber (by default) login or email  of the target user. (**). </p>

<p>
All other columns will be ignored. </p>


<p>
By default the users will be enrolled as students but you may select other roles that you are allowed to manage (teacher, non editing teacher
or any custom roles)
</p>

<p>
You may repeat this operation at will without dammages, for example if you forgot some users to unenrol.
</p>



<p>
<span <font color=\'red\'>(*) </font></span>: double quotes and spaces, added by some spreadsheet programs will be removed.
</p>

<p>
<span <font color=\'red\'>(**) </font></span>: target account must exist in Moodle and be enrolled to this course.
</p>
';
$string['mass_unenroll_info'] = '
<p>With this option you are going to unenrol a list on users from a file with one account per line.</p>
<p>Empty lines or unknown accounts will be skipped.</p>
<p>The file may contains several columns, separated by a comma, a semi-column or a tabulation.</p>
<p>Every relevant column <b>must</b> be mapped; see the "Mappings" section in the form below.</p>
<p>You may repeat this operation at will without damages, for example if you forgot the group for some users.</p>
';
$string['massenrollsettings'] = 'Mass enrol settings';
$string['other'] = 'Other settings';
$string['pluginname'] = 'Mass enrollments';
$string['privacy:metadata'] = 'The Mass Enrol local plugin does not store any personal data';
$string['roleassign'] = 'Role to assign';
$string['unenroll'] = 'Unenroll them from my course';
$string['username'] = 'Login';
