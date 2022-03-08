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
 * @package     local_forumreset
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();



function local_dexpmod_extend_settings_navigation($settingsnav, $context) {
    global $CFG, $PAGE;

    // Only add this settings item on non-site course pages.
    if (!$PAGE->course or $PAGE->course->id == 1) {
        return;
    }

    // Only let users with the appropriate capability see this settings item.
    if (!has_capability('moodle/backup:backupcourse', context_course::instance($PAGE->course->id))) {
        return;
    }

    if ($settingnode = $settingsnav->find('courseadmin', navigation_node::TYPE_COURSE)) {
        $strfoo = get_string('course_link', 'local_dexpmod');
        $url = new moodle_url('/local/dexpmod/index.php', array('id' => $PAGE->course->id));
        $foonode = navigation_node::create(
            $strfoo,
            $url,
            navigation_node::NODETYPE_LEAF,
            'dexpmod',
            'dexpmod',
            new pix_icon('i/scheduled', 'dexpmod')
        );
        if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
            $foonode->make_active();
        }
        $settingnode->add_node($foonode);
    }
}


/**
 * Returns the activities with completion set in current course
 *
 * @param int    $courseid   ID of the course
 * @param int    $config     The block instance configuration
 * @param string $forceorder An override for the course order setting
 * @return array Activities with completion settings in the course
 */
function local_dexpmod_get_activities($courseid, $config = null, $forceorder = null) {
    $modinfo = get_fast_modinfo($courseid, -1);
    $sections = $modinfo->get_sections();
    $activities = array();
    foreach ($modinfo->instances as $module => $instances) {
        $modulename = get_string('pluginname', $module);
        foreach ($instances as $index => $cm) {
            if (
                $cm->completion != COMPLETION_TRACKING_NONE && (
                    $config == null || (
                        !isset($config->activitiesincluded) || (
                            $config->activitiesincluded != 'selectedactivities' ||
                                !empty($config->selectactivities) &&
                                in_array($module.'-'.$cm->instance, $config->selectactivities))))
            ) {
                $activities[] = array (
                    'type'       => $module,
                    'modulename' => $modulename,
                    'id'         => $cm->id,
                    'instance'   => $cm->instance,
                    'name'       => format_string($cm->name),
                    'expected'   => $cm->completionexpected,
                    'section'    => $cm->sectionnum,
                    'position'   => array_search($cm->id, $sections[$cm->sectionnum]),
                    'url'        => method_exists($cm->url, 'out') ? $cm->url->out() : '',
                    'context'    => $cm->context,
                    'icon'       => $cm->get_icon_url(),
                    'available'  => $cm->available,
                );
            }
        }
    }

    // // Sort by first value in each element, which is time due.
    // if ($forceorder == 'orderbycourse' || ($config && $config->orderby == 'orderbycourse')) {
    //     usort($activities, 'block_completion_progress_compare_events');
    // } else {
    //     usort($activities, 'block_completion_progress_compare_times');
    // }

    return $activities;
}



/**
 * Returns the activities with completion set in current course
 *
 * @param int    $courseid   ID of the course
 *  @param array    $data  form data
 * @return array table of activities
 */
function list_moved_activities($courseID, $data) {
    global $DB;

    //Get all activities in the course
    $activities = local_dexpmod_get_activities($courseID, null, 'orderbycourse');
    $numactivies = count($activities);


        $add_duration = $data->timeduration;
        $sql_params = ['course' => $courseID ];
        $expected_array = $DB->get_records('course_modules',$sql_params );


        $table = new html_table();
        $table->head = array( 'Aktivität' , 'Abschlusstermin');

    foreach($activities as $index => $activity)  {        

        if($activity['expected']>0)    {
            //Activities with expected completion

            // Check if all activities should be moved
            if($data->config_activitiesincluded=='allactivites')
            {// Move all activities contained in the course

                if($data->datedependence)    {

                    $record_params = ['id' => $activity['id']];
                    $expected_old=$DB->get_record('course_modules',$record_params,$fields='*' );

                    if($data->date_min <= $expected_old->completionexpected && $expected_old->completionexpected <= $data->date_max)
                    {
                        $newdate=$expected_old->completionexpected+$add_duration;
                    $update_params = ['id' => $activity['id'], 'completionexpected' => $newdate];
                    $DB->update_record('course_modules',$update_params );
                    // To ensure a valid date read expextec completion from DB
                    $replaced_date=$DB->get_record('course_modules',$record_params,$fields='*' );
                   
                    //  echo $OUTPUT->heading($activity['name']." -> ". userdate( $replaced_date->completionexpected),5);
                     $table->data[] = array($activity['name'],userdate( $replaced_date->completionexpected));

                    }

                }

                else    {

                    $record_params = ['id' => $activity['id']];
                 $expected_old=$DB->get_record('course_modules',$record_params,$fields='*' );
                 $newdate=$expected_old->completionexpected+$add_duration;
                 $update_params = ['id' => $activity['id'], 'completionexpected' => $newdate];
                 $DB->update_record('course_modules',$update_params );
                 // To ensure a valid date read expextec completion from DB
                 $replaced_date=$DB->get_record('course_modules',$record_params,$fields='*' );
                //  echo $OUTPUT->heading($activity['name']." -> ". userdate( $replaced_date->completionexpected),5);
                 $table->data[] = array($activity['name'],userdate( $replaced_date->completionexpected));


                }
     
            }

            else    {
                // All Activities chosen by the user
                if(in_array($activity['id'], $data->selectactivities) )
                {
                    
                    $record_params = ['id' => $activity['id']];
                    $expected_old=$DB->get_record('course_modules',$record_params,$fields='*' );
                    $newdate=$expected_old->completionexpected+$add_duration;
                    $update_params = ['id' => $activity['id'], 'completionexpected' => $newdate];
                    $DB->update_record('course_modules',$update_params );
                    // To ensure a valid date read expextec completion from DB
                    $replaced_date=$DB->get_record('course_modules',$record_params,$fields='*' );
                   
                    // echo $OUTPUT->heading($activity['name']." -> ". userdate( $replaced_date->completionexpected),5);      
                    $table->data[] = array($activity['name'],userdate( $replaced_date->completionexpected));
                     
                }
            } 
    }

    }

    return $table;
}


/**
 * Returns the activities with completion set in current course
 *
 * @param int    $courseid   ID of the course
 * @return array table of activities
 */
function list_all_activities($courseID) {
    global $DB;
   //Standard values without submitting the form

   $activities = local_dexpmod_get_activities($courseID, null, 'orderbycourse');
   $numactivies = count($activities);
   
   $table = new html_table();
   $table->head = array( 'Aktivität' , 'Abschlusstermin');
   // echo $OUTPUT->heading('Kursinformationen: '.get_course($courseID)->fullname  ,2);
   $sql_params = ['course' => $courseID ];
  foreach($activities as $index => $activity)  {

   if($activity['expected']>0 )  {
       $record_params = ['id' => $activity['id']];
       $date_expected=$DB->get_record('course_modules',$record_params,$fields='*' );
       // echo $OUTPUT->heading("&nbsp"."&#8226". $activity['name'].": ".userdate($date_expected->completionexpected) ,5);
       $table->data[] = array($activity['name'],userdate($date_expected->completionexpected));
      
   }      

  }

  return $table;
}


