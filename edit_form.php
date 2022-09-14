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
 * Local plugin "DexpMod" - edit_form.php
 *
 * @package     local_dexpmod
 * @copyright   2022 Alexander Dominicus, Bochum University of Applied Science <alexander.dominicus@hs-bochum.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
require_once "$CFG->libdir/formslib.php";

class dexpmod_form extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $CFG;
        global $PAGE;
        global $DB;

        $activities = local_dexpmod_get_activities($this->_customdata['courseid'], null, 'orderbycourse');
        $numactivies = count($activities);

        $mform = $this->_form; // Don't forget the underscore!
        $attributes = ['size' => '20'];

        // $mform->addElement('text', 'addtime', 'Tage drauf rechnen', $attributes);
        $mform->setType('addtime', PARAM_INT);
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }
        $now = new DateTime('now', core_date::get_server_timezone_object());
      
        // $mform->addElement('static', '', '', get_string('headline', 'local_dexpmod'));
        // $mform->addElement('static', '', '', get_string('info', 'local_dexpmod', $a));
        $mform->addElement('duration', 'timeduration', 'Intervall');
        // Control which activities are included in the bar.
        $activitiesincludedoptions = [
            'allactivites' => 'All activities',
            'selectedactivities' => 'Selected activities only',
        ];
        $activitieslabel = 'Activities included';
        $mform->addElement('select', 'config_activitiesincluded', $activitieslabel, $activitiesincludedoptions);
        $mform->setAdvanced('config_activitiesincluded', true);
        //Chose time intervall of shifted activities. Only possible if ALL activities are chose
        $mform->addElement('advcheckbox', 'datedependence', get_string('datefilter', 'local_dexpmod'));
        $mform->addHelpButton('datedependence', 'how_date_selection_works', 'local_dexpmod');
        $mform->hideif('datedependence', 'config_activitiesincluded', 'neq', 'selectedactivities');
        // Dirty hack: user may refresh page after unchecking the date filte
        if ($this->_customdata['datemin']>0) {
            // ISSUE MDL-66251: Static element can't be hidden
            $a = new stdClass();
            $a->courseid = $this->_customdata['courseid'];
            $group = [];
            $group[] =& $mform->createElement('static', 'refresh', '',get_string('refresh', 'local_dexpmod',$a));
            $mform->addGroup($group, 'formgroup', '', ' ', false);
            $mform->hideIf('formgroup','datedependence', 'eq', '1');
        }
        $mform->addElement('date_time_selector', 'date_min', get_string('date_min', 'local_dexpmod'));
        //  $mform->hideif('date_min', 'config_activitiesincluded', 'eq', 'selectedactivities');
        $mform->hideif('date_min', 'datedependence', 'eq', '0');
        $mform->addElement('date_time_selector', 'date_max', get_string('date_max', 'local_dexpmod'));
        // $mform->hideif('date_max', 'config_activitiesincluded', 'eq', 'selectedactivities');
        $mform->hideif('date_max', 'datedependence', 'eq', '0');
        // $mform->addElement('button', 'datebutton', get_string('filterbydate', 'local_dexpmod'));

        if ($this->_customdata['datemin']>0)   {
            // date selection filter active
            $mform->setDefault('config_activitiesincluded', 'selectedactivities');
            $mform->setDefault('datedependence', 1);
            $mform->setDefault('date_min', $this->_customdata['datemin']);
            $mform->setDefault('date_max', $this->_customdata['datemax']);   
        }
        else    {
            $mform->setDefault('config_activitiesincluded', 'allactivites');
            $mform->setDefault('datedependence', 0);
            $mform->hideif('selectactivities', 'datedependence', 'eq', '1');
            
        }

        // Selected activities by the user
        $activitiestoinclude = [];
        foreach ($activities as $index => $activity) {
            if ($activity['expected'] > 0) {
                // $activitiestoinclude[$activity['type'].'-'.$activity['instance']] = $activity['name'];
                // Filtered by date
                if ($this->_customdata['datemin']>0) {
                    if ($activity['expected']>=$this->_customdata['datemin'] && $activity['expected'] <= $this->_customdata['datemax']  )   {
                        $record_params = ['id' => $activity['id']];
                        $date_expected = $DB->get_record('course_modules', $record_params, $fields = '*');
                        $activitiestoinclude[$activity['id']] = $activity['section'].': '.$activity['name'].' '.date('d.m.y-H:i', $date_expected->completionexpected);
                    }
                }
                else {
                    $record_params = ['id' => $activity['id']];
                    $date_expected = $DB->get_record('course_modules', $record_params, $fields = '*');
                    $activitiestoinclude[$activity['id']] = $activity['section'].': '.$activity['name'].' '.date('d.m.y-H:i', $date_expected->completionexpected);
                }
                
            }
        }
        $mform->addElement('select', 'selectactivities', 'select activities', $activitiestoinclude);
        $mform->getElement('selectactivities')->setMultiple(true);
        $mform->getElement('selectactivities')->setSize(count($activitiestoinclude));
        $mform->setAdvanced('selectactivities', true);
        $mform->hideif('selectactivities', 'config_activitiesincluded', 'neq', 'selectedactivities');

        if ($this->_customdata['datemin']>0)    {
            $mform->addElement('submit', 'movedates', get_string('finish', 'local_dexpmod'));
        }
        else    {
            $mform->addElement('submit', 'applydatefilter', get_string('filterbydate', 'local_dexpmod'));
            $mform->hideif('applydatefilter', 'datedependence', 'neq', '1');
            $mform->addElement('submit', 'movedates', get_string('finish', 'local_dexpmod'));
            $mform->hideif('movedates', 'datedependence', 'eq', '1');
        }
        // $this->add_action_buttons($cancel = false, $submitlabel = get_string('chageduedates', 'local_dexpmod'));
    }

    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}
