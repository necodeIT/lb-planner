/// This library contains the API, models and providers needed for the [LB Planner application](https://github.com/necodeIT/lb_planner/tree/app-stable).
library lbplanner_engine;

// TODO: Rework architecture to use service interfaces instead of concrete in providers ; concrete implementations used for os specific implementations
//TODO: Use AsyncValue for providers

export 'models/user/user.dart';
export 'models/notification/notification.dart';
export 'models/module/module.dart';
export 'models/invite/invite.dart';
export 'models/plan/plan.dart';
export 'models/course/course.dart';
export 'models/statistics/statistics.dart';
export 'models/feedback/feedback.dart';
export 'models/deadline/deadline.dart';
export 'models/updater/version.dart';
export 'models/updater/update.dart';
export 'models/updater/update_status.dart';

// Dart imports:
import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'dart:ui';

// Package imports:
import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:lbplanner_engine/models/statistics/statistics.dart';
import 'package:nekolib_utils/log.dart';
import 'package:path_provider/path_provider.dart';
import 'package:riverpod/riverpod.dart' hide StateNotifier;
import 'package:riverpod/riverpod.dart' as riverpod;

// Project imports:
import 'models/course/course.dart';
import 'models/feedback/feedback.dart';
import 'models/module/module.dart';
import 'models/notification/notification.dart';
import 'models/plan/plan.dart';
import 'models/user/user.dart';
import 'models/deadline/deadline.dart';
import 'models/invite/invite.dart';
import 'models/updater/version.dart';
import 'models/updater/update.dart';
import 'models/updater/update_status.dart';

// Models

part 'models/user/languages.dart';
part 'models/user/user_capabilities.dart';
part 'models/notification/notification_types.dart';
part 'models/notification/notification_status.dart';
part 'models/module/module_grades.dart';
part 'models/module/module_types.dart';
part 'models/module/module_status.dart';
part 'models/plan/plan_access_levels.dart';
part 'models/invite/invite_status.dart';
part 'models/feedback/feedback_types.dart';
part 'models/feedback/feedback_status.dart';
part 'models/updater/setup_type.dart';
part 'models/updater/download_status.dart';

// Services

part 'services/internet_service/internet_service.dart';
part 'services/internet_service/internet_adress_lookup_service.dart';
part 'services/internet_service/internet_connectivity_service.dart';
part 'services/updater/updater_service.dart';
part 'services/updater/app_image_updater_service.dart';
part 'services/updater/dmg_updater_service.dart';
part 'services/updater/aur_updater_service.dart';
part 'services/updater/aur_git_updater_service.dart';
part 'services/updater/exe_updater_service.dart';
part 'services/updater/self_compiled_updater_service.dart';

// Providers

part 'providers/user_provider.dart';
part 'providers/notifications_provider.dart';
part 'providers/modules_provider.dart';
part 'providers/courses_provider.dart';
part 'providers/feedback_provider.dart';
part 'providers/statistics_provider.dart';
part 'providers/users_provider.dart';
part 'providers/invites_provider.dart';
part 'providers/plan_provider.dart';
part 'providers/internet_provider.dart';
part 'providers/update_provider.dart';

// Api

part 'api/api.dart';
part 'models/api/api_response.dart';
part 'api/user_api.dart';
part 'models/api/api_services.dart';
part 'api/notifications_api.dart';
part 'api/plan_api.dart';
part 'api/feedback_api.dart';
part 'api/modules_api.dart';
part 'api/courses_api.dart';
part 'api/plugin_config_api.dart';

// Utils

part 'utils/map_extensions.dart';
part 'utils/color_extensions.dart';
part 'utils/bool_extensions.dart';
part 'utils/response.dart';
part 'utils/provider_utils.dart';
part 'utils/date_time_extensions.dart';
part 'utils/refreshable.dart';
part 'utils/state_notifier.dart';
part 'utils/user_utils.dart';
part 'utils/string_utils.dart';

// Disk

part 'disk/user_disk.dart';
part 'disk/disk.dart';

/// The rate with which the api refreshes underlying data
const kApiRefreshRate = Duration(seconds: 10);

/// Specifies how the app was installed and how it should be updated.
final kSetupType = SetupTypeFromString.fromEnvironment();
