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






$PAGE->set_title($SITE->fullname . ': ' . "Name1");
$PAGE->set_heading($SITE->fullname);
// $PAGE->set_url(new moodle_url('/local/dexmod/index.php'));
$PAGE->navbar->ignore_active(true);
// $PAGE->navbar->add("Dexpmod", new moodle_url('/local/dexpmod/index.php'));
$PAGE->navbar->add("Dexpmod", new moodle_url($url));
$PAGE->set_pagelayout('admin');
echo $OUTPUT->header();


$mform = new dexpmod_form(new moodle_url($url));
if ($mform->is_cancelled()){
    $cancelurl = new moodle_url('/course/view.php', $currentparams);
     redirect($cancelurl);
}


elseif($data = $mform->get_data()) {

   

// $add= $data->addtime;
$add_duration = $data->timeduration;
    $sql = "
            SELECT `completionexpected` FROM `mdl_course_modules` WHERE `course`=$courseID  AND `module`=4
        ";
        // Get all available questions.
        // $expected = $DB->get_record($sql);
    
        $sql_params = ['course' => $courseID ];
        $expected_array = $DB->get_records('course_modules',$sql_params );

    echo "  ";
    // print var_dump(\count($expected_array));
    //  print var_dump(\reset($expected_array)->completionexpected);


     // One day are 86400 sec. So add this amount of seconds to add a whole day
     $day= 86400;


     echo "Kurs-ID: ";
     echo $courseID;
     echo nl2br("\n") ;

    foreach($expected_array as $entry)  {



        

        if($entry->completionexpected>0)    {

            if($data->date_min <= $entry->completionexpected)
            {
                // $newdate=$entry->completionexpected+$add*$day;
                $newdate=$entry->completionexpected+$add_duration;

                $update_params = ['id' => $entry->id, 'completionexpected' => $newdate];

                $DB->update_record('course_modules',$update_params );
                $name =  $DB->get_record('modules', array('id'=>$entry->module));

                    
                    echo "Activty: ";
                    echo $name->name;
                    echo " was shifted. ";
                    echo "Newdate: ";
                    echo userdate($newdate);
                    echo nl2br("\n") ;
                                
            }

            else    {
                $name =  $DB->get_record('modules', array('id'=>$entry->module));
                echo "Activty: ";
                echo $name->name;;
                echo " not shifted ";
                echo nl2br("\n") ;
            }



    }
    else    {
        $name =  $DB->get_record('modules', array('id'=>$entry->module));
        echo "Activty: ";
        echo $name->name;;
        echo " not shifted ";
        echo nl2br("\n") ;
    }

    }

//  redirect(new moodle_url($url ));

}

else {
    echo 'useful informations:';
    echo nl2br("\n") ;
    $sql_params = ['course' => $courseID ];
    $expected_array = $DB->get_records('course_modules',$sql_params );

    echo "Kurs-ID: ";
    echo $courseID;
    echo nl2br("\n") ;

   foreach($expected_array as $entry)  {

    if($entry->completionexpected>0 )  {
        $name =  $DB->get_record('modules', array('id'=>$entry->module));

                   
        echo "Activty: ";
        echo $name->name;
        echo ', expected: ';
        echo userdate($entry->completionexpected);
        echo nl2br("\n") ;
                    

    }

    else    {
        $name =  $DB->get_record('modules', array('id'=>$entry->module));

                   
        echo "Activty: ";
        echo $name->name;
        echo ', no activity completion expected!';
        echo nl2br("\n") ;
    }

              
           

   }
    
}
//displays the form




$mform->display();


echo $OUTPUT->footer();
