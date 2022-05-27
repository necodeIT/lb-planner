library lbplanner_routes;

import 'dart:io';
import 'dart:math';
import 'package:animations/animations.dart';
import 'package:badges/badges.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:context_menus/context_menus.dart';
import 'package:enum_to_string/enum_to_string.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart' hide Notification, Feedback;
import 'package:flutter_cache_manager/flutter_cache_manager.dart';
import 'package:flutter_colorpicker/flutter_colorpicker.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_sticky_header/flutter_sticky_header.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:intl/intl.dart';
import 'package:lb_planner/assets.dart';
import 'package:lb_planner/helpers.dart';
import 'package:lb_planner/widgets.dart';
import 'package:lbplanner_api/lbplanner_api.dart';
import 'package:nekolib_ui/core.dart';
import 'package:nekolib_ui/utils.dart';
import 'package:nekolib_utils/extensions.dart';
import 'package:nekolib_utils/log.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:window_manager/window_manager.dart';
import 'package:timeago/timeago.dart' as timeago;

part 'routes/login/login/login.dart';
part 'routes/login/login/background.dart';
part 'routes/dashboard/dashboard.dart';
part 'routes/route/route.dart';
part 'routes/route/sidebar/sidebar.dart';
part 'routes/login/login/login_form.dart';
part 'routes/route/sidebar/sidebar_item.dart';
part 'routes/route/user/user_profile.dart';
part 'routes/route/route_title.dart';
part 'routes/calendar/plan/calendar_plan.dart';
part 'routes/settings/settings.dart';
part 'routes/test/test.dart';
part 'routes/route/user/user_notifications.dart';
part 'routes/route/user/user_notifications_popup.dart';
part 'routes/dashboard/status_overview/status_overview.dart';
part 'routes/dashboard/status_overview/bar_label.dart';
part 'routes/dashboard/status_overview/bar.dart';
part 'routes/dashboard/status_overview/chart.dart';
part 'routes/settings/themes/themes.dart';
part 'routes/settings/themes/theme_item.dart';
part 'routes/settings/courses/courses.dart';
part 'routes/settings/courses/course_item.dart';
part 'routes/dashboard/todays_tasks/todays_tasks.dart';
part 'routes/dashboard/exams/exams.dart';
part 'routes/route/user/user_notifications_item.dart';
part 'routes/settings/feedback/feedback.dart';
part 'routes/login/select_courses/select_courses.dart';
part 'routes/login/select_courses/svg.dart';
part 'routes/login/select_courses/course_selection.dart';
part 'routes/calendar/modules_overview/calendar_modules_overview.dart';
part 'routes/calendar/calendar.dart';
part 'routes/calendar/modules_overview/course_modules_overview.dart';
part 'routes/calendar/modules_overview/calendar_modules_overview_cell.dart';
part 'routes/calendar/modules_overview/calendar_modules_overview_month_header.dart';
part 'routes/calendar/plan/calendar_plan_cell.dart';
part 'routes/calendar/plan/calendar_plan_month.dart';
part 'routes/calendar/plan/calendar_plan_month_navigator.dart';
part 'routes/calendar/plan/dropdown/dropdown_modules.dart';
part 'routes/calendar/plan/dropdown/dropdown_members.dart';
part 'routes/calendar/plan/dragable_module.dart';
part 'routes/calendar/plan/dropdown/dropdown_body.dart';
part 'routes/calendar/plan/dropdown/dropdown_header.dart';
part 'routes/calendar/plan/dropdown/dropdown_members_member.dart';
part 'routes/admin/login/login.dart';
part 'routes/admin/dashboard/dashboard.dart';
part 'routes/calendar/plan/dropdown/dropdown_members_invite_users.dart';
part 'routes/calendar/plan/dropdown/dropdown_members_invite_users_user.dart';
part 'routes/route/user/user_profile_img.dart';
part 'routes/offline/offline.dart';
part 'routes/settings/general/general.dart';
part 'routes/settings/general/general_item.dart';
part 'routes/settings/general/credits.dart';
part 'routes/settings/courses/edit_dialog_body.dart';
part 'routes/settings/courses/edit_dialog_header.dart';
part 'routes/admin/dashboard/section.dart';
part 'routes/admin/dashboard/svg.dart';
part 'routes/admin/feedback/feedback.dart';
part 'routes/admin/feedback/feedback_item.dart';
part 'routes/admin/feedback/feedback_page.dart';

/// All routes the app has
final Map<String, RouteInfo> kRoutes = {
  LoginRoute.info.routeName: LoginRoute.info,
  DashboardRoute.info.routeName: DashboardRoute.info,
  CalendarPlanRoute.info.routeName: CalendarPlanRoute.info,
  SettingsRoute.info.routeName: SettingsRoute.info,
  LoginSelectCoursesRoute.info.routeName: LoginSelectCoursesRoute.info,
  CalendarModulesOverviewRoute.info.routeName: CalendarModulesOverviewRoute.info,
  AdminLoginRoute.info.routeName: AdminLoginRoute.info,
  OfflineRoute.info.routeName: OfflineRoute.info,
  AdminDashboardRoute.info.routeName: AdminDashboardRoute.info,
  SettingsGeneralCreditsRoute.info.routeName: SettingsGeneralCreditsRoute.info,
  AdminFeedbackRoute.info.routeName: AdminFeedbackRoute.info,
  AdminFeedbackPageRoute.info.routeName: AdminFeedbackPageRoute.info,
  if (kDebugMode) TestRoute.info.routeName: TestRoute.info,
};

/// Observes navigation events.
final RouteObserver<ModalRoute<void>> kRouteObserver = RouteObserver<ModalRoute<void>>();
