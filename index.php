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
 * Local plugin "DexpMod" - index.php
 *
 * @package     local_dexpmod
 * @copyright   2022 Alexander Dominicus, Bochum University of Applied Science <alexander.dominicus@hs-bochum.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once __DIR__.'/../../config.php';
require_once $CFG->libdir.'/adminlib.php';
require_once 'lib.php';
require_once 'edit_form.php';

 require_login();

global $CFG, $DB, $PAGE;
$courseID = required_param('id', PARAM_INT);
$datemin = optional_param('datemin',0, PARAM_INT);
$datemax = optional_param('datemax',0, PARAM_INT);
$reviewselection = optional_param('reviewselection',0, PARAM_INT);
$course = $DB->get_record('course', ['id' => $courseID]);
$coursecontext = context_course::instance($course->id);

$currentparams = ['id' => $courseID];
$url = new moodle_url('/local/dexpmod/index.php', $currentparams);
$PAGE->set_url($url);
if (!has_capability('local/dexpmod:movedates', $coursecontext)) {
    $url_back = new moodle_url('/my');
    redirect($url_back, 'sie haben nicht die passenden Berechtigungen!', null, \core\output\notification::NOTIFY_ERROR);
}

// Set page context.
$PAGE->set_context(context_system::instance());
// Set page layout.
$PAGE->set_pagelayout('standard');
// Set page layout.

$PAGE->set_title($SITE->fullname.': '.'DexpMod');
$PAGE->set_heading($SITE->fullname);
// $PAGE->set_url(new moodle_url('/local/dexmod/index.php'));
$PAGE->navbar->ignore_active(true);
// $PAGE->navbar->add("Dexpmod", new moodle_url('/local/dexpmod/index.php'));
$PAGE->navbar->add('addbe', new moodle_url($url));
$PAGE->set_pagelayout('admin');

$mform = new dexpmod_form(null, ['courseid' => $courseID,'datemin' => $datemin,'datemax' => $datemax, 'url' => $url]);
//display the form

// $mform->set_data((object)$currentparams);
if ($data = $mform->get_data()) {

    if(count($data->selectactivities)>0 || $data->config_activitiesincluded == 'allactivites') {
        move_activities($courseID, $data);
        redirect(new moodle_url('/local/dexpmod/index.php', $currentparams), "Daten wurden geändert!");
    }
    elseif ($data->datedependence )  {
        $filterparams = ['id' => $courseID ,'datemin'=> $data->date_min, 'datemax'=>$data->date_max];
        redirect(new moodle_url('/local/dexpmod/index.php', $filterparams));
    }
    
}
echo $OUTPUT->header();
$a = new stdClass();
$a->course = get_course($courseID)->fullname;
$a->datemin = userdate(1633039200);
$a->datemax = userdate(1635721140);
echo html_writer::tag('h2',get_string('headline', 'local_dexpmod'));
echo html_writer::tag('p',get_string('info', 'local_dexpmod',$a));
$mform->display();
$backurl = new moodle_url('/course/view.php', ['id' => $courseID]);
echo $OUTPUT->single_button($backurl, get_string('backtocourse', 'local_dexpmod'), 'get');
if($datemin) {
    $table = list_all_activities($courseID,$datemin,$datemax);
    echo html_writer::tag('h3',"List of filtered activities");
    echo html_writer::table($table);
}
else {
    $table = list_all_activities($courseID);
    echo html_writer::tag('h3',"List of all activities");
    echo html_writer::table($table);
}


echo $OUTPUT->footer();
