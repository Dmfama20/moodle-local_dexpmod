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
 * You may localized strings in your plugin
 *
 * @package    local_dexpmod
 * @copyright  2014 Daniel Neis
 * @license    http://www.gnu.org/copyleft/gpl.html gnu gpl v3 or later
 */

$string['pluginname'] = 'DexpMod';
$string['course_link'] = 'DexpMod'; 
$string['semester_begin'] = 'Semester Start';
$string['finish'] = 'los!';
$string['info'] = '<p>In the table below you will find all activities of the course <i>{$a->course}</i> where an activity completion date is enabled.</p> 
<p>You can move all listed activities by selecting a time intervall 
and pressing the submit button. By enabling the activity date checkbox you can chose upper and lower bounds of moved acitivities.</p> 
I.e. if you want to move all activities with expected date in October 2021 you can chose upper and lower dates equal to <p> <i> {$a->datemin} and {$a->datemax}. </i> </p>
For moving only selected acitites choose "selected activies only". Then you can select/unselect all activies which you want to move manually.
<p>If you want to refresh the table below,  
<a href="index.php?id={$a->courseid}">please click here!</a> </p> ';
$string['date_min'] = 'Mindest Datum';
$string['how_date_selection_works'] = 'How date selection works';
$string['how_date_selection_works_help'] = 'Chose lower and upper date for shifting. This will only work if you chose >>all activities<< in the dropdown above!';