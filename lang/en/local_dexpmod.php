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
 * Local plugin "DexpMod" - local_dexpmod.php
 *
 * @package     local_dexpmod
 * @copyright   2022 Alexander Dominicus, Bochum University of Applied Science <alexander.dominicus@hs-bochum.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$string['pluginname'] = 'addbe';
$string['course_link'] = 'addbe'; 
$string['dexpmod:movedates'] = 'move dates';
$string['semester_begin'] = 'Semester Start';
$string['finish'] = 'move dates!';
$string['section'] = 'section';
$string['activity'] = 'activity';
$string['duedate'] = 'duedate';
$string['chageduedates'] = 'check your selection!';
$string['backtocourse'] = 'Back to course!';
$string['headline'] = 'Move your activity dates in bulk!';
$string['info'] = '<p>In the table below you will find all activities of the course <i>{$a->course}</i> where an activity completion date is enabled.</p> 
<p>You can move all listed activities by selecting a time intervall 
and pressing the submit button. By enabling the activity date checkbox you can chose upper and lower bounds of moved acitivities.</p> 
I.e. if you want to move all activities with expected date in October 2021 you can chose upper and lower dates equal to <p> <i> {$a->datemin} and {$a->datemax}. </i> </p>
For moving only selected acitites choose "selected activies only". Then you can select/unselect all activies which you want to move manually. ';
$string['date_min'] = 'date min';
$string['date_max'] = 'date max';
$string['filterbydate'] = 'apply date filter';
$string['datefilter'] = 'filter by date';
$string['how_date_selection_works'] = 'Date selection';
$string['how_date_selection_works_help'] = 'Chose lower and upper date for shifting. This will only work if you chose >>all activities<< in the dropdown above!';