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
 * Provides {@link format_remuiformat\external\move_activity_to_section} trait.
 *
 * @package     format_remuiformat
 * @category    external
 * @copyright   2018 Wisdmlabs
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_remuiformat\external;
defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_value;

require_once($CFG->libdir.'/externallib.php');
require_once($CFG->dirroot.'/course/lib.php');

/**
 * Trait implementing the external function format_remuiformat_move_activity_to_section
 */
trait move_activity_to_section {

    /**
     * Describes the structure of parameters for the function.
     *
     * @return external_function_parameters
     */
    public static function move_activity_to_section_parameters() {
        return new external_function_parameters(
            array (
                'courseid' => new external_value(PARAM_INT, 'Course Id'),
                'sectionid' => new external_value(PARAM_INT, 'Section Id'),
                'activityidtomove' => new external_value(PARAM_RAW, 'Activity Id'),
            )
        );
    }

    public static function move_activity_to_section($courseid, $sectionid, $activityidtomove) {
        global $DB, $CFG;
        
        // Get course object
        $course = get_course($courseid);
        
        // Get activity object.
        $cm = get_coursemodule_from_id(null, $activityidtomove, $courseid, false, MUST_EXIST);
        require_login($course, false, $cm);
        
        // Get new section object.
        $section = $DB->get_record('course_sections', array('course' => $courseid, 'section' => $sectionid));
        
        // Move the activity to new section. Function moveto_module() define in /course/lib.php.
        if( moveto_module($cm, $section, '') ) {
            // Generate new URL to redirect with new sction.
            $urltogo = $CFG->wwwroot.'/course/view.php?id=' . $courseid . '&section=' . $sectionid;
        } else {
            // Redirect to course URL.
            $urltogo = $CFG->wwwroot.'/course/view.php?id=' . $courseid;
        }

        $output = array();
        $output['urltogo'] = $urltogo;
        return $output;
    }

    public static function move_activity_to_section_returns() {
        return new \external_single_structure (
            array(
                'urltogo' => new external_value(PARAM_RAW, 'If error occurs or not.'),
            )
        );
    }
}
