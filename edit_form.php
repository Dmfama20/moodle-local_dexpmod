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
 * Library of functions for local_differentiator.
 *
 * @package     local_dexpmod
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class dexpmod_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;
        global $PAGE;

        $activities = local_dexpmod_get_activities($this->_customdata['courseid'], null, 'orderbycourse');
        $numactivies = count($activities);
       
        
       
        $mform = $this->_form; // Don't forget the underscore! 
        $attributes=array('size'=>'20');

       
        // $mform->addElement('text', 'addtime', 'Tage drauf rechnen', $attributes);
        $mform->setType('addtime', PARAM_INT);
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }
        $now = new DateTime("now", core_date::get_server_timezone_object());

       
        
        
        $a = new stdClass();
        $a->course = get_course($this->_customdata['courseid'])->fullname ;
        $a->courseid=$this->_customdata['courseid'];
        $a->datemin = userdate(1633039200);
        $a->datemax = userdate(1635721140);
        $mform->addElement('static', '', '', get_string('info', 'local_dexpmod', $a));
        $mform->addElement('duration', 'timeduration', 'Intervall');
        // Control which activities are included in the bar.
        $activitiesincludedoptions = array(
            'allactivites' => 'All activities',
            'selectedactivities' => 'Selected activities only',
        );
        $activitieslabel = 'Activities included';
        $mform->addElement('select', 'config_activitiesincluded', $activitieslabel, $activitiesincludedoptions);
        $mform->setDefault('config_activitiesincluded','allactivites' );
        $mform->setAdvanced('config_activitiesincluded', true);
        //Chose time intervall of shifted activities. Only possible if ALL activities are chose
        $mform->addElement('advcheckbox', 'datedependence', 'Dependence on acitity dates');
        $mform->addHelpButton('datedependence','how_date_selection_works','local_dexpmod');
        $mform->hideif('datedependence', 'config_activitiesincluded', 'eq', 'selectedactivities');
        $mform->addElement('date_time_selector', 'date_min', get_string('date_min', 'local_dexpmod'));
        $mform->hideif('date_min', 'config_activitiesincluded', 'eq', 'selectedactivities');
        $mform->hideif('date_min', 'datedependence', 'eq', '0');
        $mform->addElement('date_time_selector', 'date_max', 'maximal Datum');
        $mform->hideif('date_max', 'config_activitiesincluded', 'eq', 'selectedactivities');
        $mform->hideif('date_max', 'datedependence', 'eq', '0');
       

        // Selected activities by the user
        $activitiestoinclude = array();
        foreach ($activities as $index => $activity) {
            if($activity['expected']>0)
            {
            // $activitiestoinclude[$activity['type'].'-'.$activity['instance']] = $activity['name'];
            $activitiestoinclude[$activity['id']] = $activity['name'];
            }
        }
        $mform->addElement('select', 'selectactivities', 'select activities', $activitiestoinclude);  
        $mform->getElement('selectactivities')->setMultiple(true);
        $mform->getElement('selectactivities')->setSize(count($activitiestoinclude));    
        $mform->setAdvanced('selectactivities', true);
        $mform->hideif('selectactivities', 'config_activitiesincluded', 'neq', 'selectedactivities');
        // $mform->addElement('submit', 'submitbutton', get_string('finish', 'local_dexpmod'));
        $this->add_action_buttons($cancel = false, $submitlabel='Ändern!');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}

