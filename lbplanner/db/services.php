<?php
// This file is part of the LB Planner plugin.
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


defined('MOODLE_INTERNAL') or die();

$functions = array(
    'local_lbplanner_user_get_user' => array(
        'classname' => 'local_lbplanner_services\user_get_user',
        'methodname' => 'get_user',
        'classpath' => 'local/lbplanner/services/user/get_user.php',
        'description' => 'Get the data for a user',
        'type' => 'read',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_user_register_user' => array(
        'classname' => 'local_lbplanner_services\user_register_user',
        'methodname' => 'register_user',
        'classpath' => 'local/lbplanner/services/user/register_user.php',
        'description' => 'Register a new user',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_user_update_user' => array(
        'classname' => 'local_lbplanner_services\user_update_user',
        'methodname' => 'update_user',
        'classpath' => 'local/lbplanner/services/user/update_user.php',
        'description' => 'Update the data for a user',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_user_delete_user' => array(
        'classname' => 'local_lbplanner_services\user_delete_user',
        'methodname' => 'delete_user',
        'classpath' => 'local/lbplanner/services/user/delete_user.php',
        'description' => 'Delete a user',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_user_refresh_permissions' => array(
        'classname' => 'local_lbplanner_services\user_refresh_permissions',
        'methodname' => 'refresh_permissions',
        'classpath' => 'local/lbplanner/services/user/refresh_permissions.php',
        'description' => 'Recheck the permissions for a user',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_course_get_course' => array(
        'classname' => 'local_lbplanner_services\course_get_course',
        'methodname' => 'get_course',
        'classpath' => 'local/lbplanner/services/course/get_course.php',
        'description' => 'Get the data for a course',
        'type' => 'read',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_course_get_all_courses' => array(
        'classname' => 'local_lbplanner_services\course_get_all_courses',
        'methodname' => 'get_all_courses',
        'classpath' => 'local/lbplanner/services/course/get_all_courses.php',
        'description' => 'Get all the courses of the current year',
        'type' => 'read',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_course_update_course' => array(
        'classname' => 'local_lbplanner_services\course_update_course',
        'methodname' => 'update_course',
        'classpath' => 'local/lbplanner/services/course/update_course.php',
        'description' => 'Update the data for a course',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_modules_get_module' => array(
        'classname' => 'local_lbplanner_services\modules_get_module',
        'methodname' => 'get_module',
        'classpath' => 'local/lbplanner/services/modules/get_module.php',
        'description' => 'Get the data for a module',
        'type' => 'read',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_modules_get_all_modules' => array(
        'classname' => 'local_lbplanner_services\modules_get_all_modules',
        'methodname' => 'get_all_modules',
        'classpath' => 'local/lbplanner/services/modules/get_all_modules.php',
        'description' => 'Get all the modules of the current year',
        'type' => 'read',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_modules_get_all_course_modules' => array(
        'classname' => 'local_lbplanner_services\modules_get_all_course_modules',
        'methodname' => 'get_all_course_modules',
        'classpath' => 'local/lbplanner/services/modules/get_all_course_modules.php',
        'description' => 'Get all the modules of the given course',
        'type' => 'read',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_plan_clear_plan' => array(
        'classname' => 'local_lbplanner_services\plan_clear_plan',
        'methodname' => 'clear_plan',
        'classpath' => 'local/lbplanner/services/plan/clear_plan.php',
        'description' => 'Clear the plan for the given user',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_plan_get_plan' => array(
        'classname' => 'local_lbplanner_services\plan_get_plan',
        'methodname' => 'get_plan',
        'classpath' => 'local/lbplanner/services/plan/get_plan.php',
        'description' => 'Get the plan of the given user',
        'type' => 'read',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_plan_invite_user' => array(
        'classname' => 'local_lbplanner_services\plan_invite_user',
        'methodname' => 'invite_user',
        'classpath' => 'local/lbplanner/services/plan/invite_user.php',
        'description' => 'Invite a user to the plan',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_plan_remove_user' => array(
        'classname' => 'local_lbplanner_services\plan_remove_user',
        'methodname' => 'remove_user',
        'classpath' => 'local/lbplanner/services/plan/remove_user.php',
        'description' => 'Remove a user from the plan',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_plan_update_plan' => array(
        'classname' => 'local_lbplanner_services\plan_update_plan',
        'methodname' => 'update_plan',
        'classpath' => 'local/lbplanner/services/plan/update_plan.php',
        'description' => 'Update the plan of the given user',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_plan_leave_plan' => array(
        'classname' => 'local_lbplanner_services\plan_leave_plan',
        'methodname' => 'leave_plan',
        'classpath' => 'local/lbplanner/services/plan/leave_plan.php',
        'description' => 'Leave the plan of the given user',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_planner_plan_add_deadline' => array(
        'classname' => 'local_lbplanner_services\plan_add_deadline',
        'methodname' => 'add_deadline',
        'classpath' => 'local/lbplanner/services/plan/add_deadline.php',
        'description' => 'Add a deadline to the plan',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true,
    ),
    'loacl_planner_plan_get_deadline' => array(
        'classname' => 'local_lbplanner_services\plan_get_deadline',
        'methodname' => 'get_deadline',
        'classpath' => 'local/lbplanner/services/plan/get_deadline.php',
        'description' => 'Get a deadline from the plan',
        'type' => 'read',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_plan_delete_deadline' => array(
        'classname' => 'local_lbplanner_services\plan_delete_deadline',
        'methodname' => 'delete_deadline',
        'classpath' => 'local/lbplanner/services/plan/delete_deadline.php',
        'description' => 'Delete a deadline from the plan',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_plan_update_deadline' => array(
        'classname' => 'local_lbplanner_services\plan_update_deadline',
        'methodname' => 'update_deadline',
        'classpath' => 'local/lbplanner/services/plan/update_deadline.php',
        'description' => 'Update a deadline from the plan',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_plan_update_invite' => array(
        'classname' => 'local_lbplanner_services\plan_update_invite',
        'methodname' => 'update_invite',
        'classpath' => 'local/lbplanner/services/plan/update_invite.php',
        'description' => 'Update a invite from the plan',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true,
    ),
    'local_lbplanner_get_invites' => array(
        'classname' => 'local_lbplanner_services\get_invites',
        'methodname' => 'get_invites',
        'classpath' => 'local/lbplanner/services/get_invites.php',
        'description' => 'Get all the invites of the given user',
        'type' => 'read',
        'capabilities' => '',
        'ajax' => true,
    ),
);

$services = array(
    'LB Planner API' => array(
        'functions' => array(
            'local_lbplanner_user_get_user',
            'local_lbplanner_user_register_user',
            'local_lbplanner_user_update_user',
            'local_lbplanner_user_delete_user',
            'local_lbplanner_user_refresh_permissions',
            'local_lbplanner_course_get_all_courses',
            'local_lbplanner_course_get_course',
            'local_lbplanner_course_update_course',
            'local_lbplanner_modules_get_all_course_modules',
            'local_lbplanner_modules_get_all_modules',
            'local_lbplanner_modules_get_module',
            'local_lbplanner_plan_add_deadline',
            'local_lbplanner_plan_clear_plan',
            'local_lbplanner_plan_delete_deadline',
            'local_lbplanner_plan_get_deadline',
            'local_lbplanner_plan_get_plan',
            'local_lbplanner_plan_invite_user',
            'local_lbplanner_plan_update_invite',
            'local_lbplanner_plan_get_invites',
            'local_lbplanner_plan_leave_plan',
            'local_lbplanner_plan_remove_user',
            'local_lbplanner_plan_update_deadline',
            'local_lbplanner_plan_update_plan',
        ),
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'lpa'
    ),
);
