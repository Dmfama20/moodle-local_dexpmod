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
 * Local Differentiator main view.
 *
 * @package     local_dexmod
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once('lib.php');
require_once('edit_form.php');

 require_login();

global $CFG, $DB, $PAGE;
$courseID = required_param('id', PARAM_INT);
$currentparams = ['id' => $courseID];

  $url = new moodle_url('/local/dexpmod/index.php', $currentparams);

 $PAGE->set_url($url);





// Set page context.
$PAGE->set_context(context_system::instance());
// Set page layout.
$PAGE->set_pagelayout('standard');
// Set page layout.

$PAGE->set_title($SITE->fullname . ': ' . "DexpMod");
$PAGE->set_heading($SITE->fullname);
// $PAGE->set_url(new moodle_url('/local/dexmod/index.php'));
$PAGE->navbar->ignore_active(true);
// $PAGE->navbar->add("Dexpmod", new moodle_url('/local/dexpmod/index.php'));
$PAGE->navbar->add("Dexpmod", new moodle_url($url));
$PAGE->set_pagelayout('admin');
echo $OUTPUT->header();
echo $OUTPUT->heading('DexpMod',1);


$mform = new dexpmod_form(null, array('courseid'=>$courseID ));
if ($mform->is_cancelled()){
    $cancelurl = new moodle_url('/course/view.php', $currentparams);
     redirect($cancelurl);
}


elseif($data = $mform->get_data()) {
    //DATA Submited

    //Get all activities in the course
    $activities = local_dexpmod_get_activities($courseID, null, 'orderbycourse');
    $numactivies = count($activities);


        $add_duration = $data->timeduration;
        $sql_params = ['course' => $courseID ];
        $expected_array = $DB->get_records('course_modules',$sql_params );

        echo $OUTPUT->heading('Folgende Aktivitäten wurden verschoben: ' ,3);

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
                   
                    echo $OUTPUT->heading($activity['name']." -> ". userdate( $replaced_date->completionexpected),5);

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
                
                 echo $OUTPUT->heading($activity['name']." -> ". userdate( $replaced_date->completionexpected),5);

                }

                 
                

            }

            else    {
                if(in_array($activity['id'], $data->selectactivities) )
                {
                    // All Activities chosen by the user
                    $record_params = ['id' => $activity['id']];
                    $expected_old=$DB->get_record('course_modules',$record_params,$fields='*' );
                    $newdate=$expected_old->completionexpected+$add_duration;
                    $update_params = ['id' => $activity['id'], 'completionexpected' => $newdate];
                    $DB->update_record('course_modules',$update_params );
                    // To ensure a valid date read expextec completion from DB
                    $replaced_date=$DB->get_record('course_modules',$record_params,$fields='*' );
                   
                    echo $OUTPUT->heading($activity['name']." -> ". userdate( $replaced_date->completionexpected),5);                           
                }

            }

           

    }


    }

}

else {

    //Standard values without submitting the form

    $activities = local_dexpmod_get_activities($courseID, null, 'orderbycourse');
    $numactivies = count($activities);
    

    echo $OUTPUT->heading('Kursinformationen : '.get_course($courseID)->fullname  ,2);
    $sql_params = ['course' => $courseID ];
    echo $OUTPUT->heading('Aktivitäten mit Abschlusstermin:',4);  

   foreach($activities as $index => $activity)  {

    if($activity['expected']>0 )  {
        $record_params = ['id' => $activity['id']];
        $date_expected=$DB->get_record('course_modules',$record_params,$fields='*' );
        echo $OUTPUT->heading("&nbsp"."&#8226". $activity['name'].": ".userdate($date_expected->completionexpected) ,5);
       
    }      

   }
    
}
//displays the form
$mform->display();
echo $OUTPUT->footer();
