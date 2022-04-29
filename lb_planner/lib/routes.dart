library lbplanner_routes;

import 'package:animations/animations.dart';
import 'package:badges/badges.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:enum_to_string/enum_to_string.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart' hide Notification;
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import 'package:lb_planner/updater.dart';
import 'package:lb_planner/widgets.dart';
import 'package:lbplanner_api/lbplanner_api.dart';
import 'package:nekolib_ui/core.dart';
import 'package:nekolib_ui/utils.dart';
import 'package:nekolib_utils/extensions.dart';
import 'package:nekolib_utils/log.dart';

part 'routes/login/login.dart';
part 'routes/login/background.dart';
part 'routes/dashboard/dashboard.dart';
part 'routes/route/route.dart';
part 'routes/route/sidebar/sidebar.dart';
part 'routes/login/login_form.dart';
part 'routes/route/sidebar/sidebar_item.dart';
part 'routes/route/user/user_profile.dart';
part 'routes/route/utils.dart';
part 'routes/route/route_title.dart';
part 'routes/calendar/calendar_plan.dart';
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
part 'routes/dashboard/todays_tasks/item.dart';
part 'routes/dashboard/exams/exams.dart';
part 'routes/route/user/user_notifications_item.dart';

/// All routes the app has
final Map<String, WidgetBuilder> kRoutes = {
  LoginRoute.routeName: (context) => LoginRoute(),
  DashboardRoute.routeName: (context) => DashboardRoute(),
  CalendarPlanRoute.routeName: (context) => CalendarPlanRoute(),
  SettingsRoute.routeName: (context) => SettingsRoute(),
  if (kDebugMode) TestRoute.routeName: (context) => TestRoute(),
};

/// Observes navigation events.
final RouteObserver<ModalRoute<void>> kRouteObserver = RouteObserver<ModalRoute<void>>();
