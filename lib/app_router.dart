import 'package:lb_planner/features/slot_booking/presentation/screens/slot_booking_screen.dart';
import 'package:lb_planner/features/themes/themes.dart';
import 'package:lb_planner/shared/shared.dart';
import 'package:auto_route/auto_route.dart';
import 'package:lb_planner/features/auth/auth.dart';

part 'app_router.gr.dart';

/// The application router.
/// Contains all routes of the application.
///
/// ---
/// In order to add a route to the application, do the following:
///
/// 1. Add `@RoutePage()` to your screen widget:
///
/// ```dart
/// import 'package:lb_planner/shared/shared.dart';
///
/// @PageRoute() // <-- add this
/// class MyScreen extends StatelessWidget {
///   const MyScreen({Key? key}) : super(key: key);
///
///   @override
///   Widget build(BuildContext context) {
///     return const Scaffold(
///       body: Center(
///         child: Text('My Screen'),
///       ),
///    );
///  }
/// }
/// ```
///
/// 2. If you want the screen to have a sidebar, add [SidebarWrapperMixin] to your screen widget:
///
/// ```dart
/// import 'package:lb_planner/shared/shared.dart';
///
/// @PageRoute()
/// class MyScreen extends StatelessWidget with SidebarWrapperMixin  {
/// //                                          ^^^^^^^^^^^^^^^^^^^ <-- add this
///   const MyScreen({Key? key}) : super(key: key);
///
///   @override
///   Widget build(BuildContext context) {
///     return const Scaffold(
///       body: Center(
///         child: Text('My Screen'),
///       ),
///    );
///  }
/// }
/// ```
///
///
/// 3. Run `build_runner`
///
/// ```bash
/// flutter pub run build_runner build --delete-conflicting-outputs
/// ```
///
/// 4. Import your screen in `app_router.dart`:
///
/// ```dart
/// import 'package:lb_planner/path/to/my/screen.dart'
/// ```
///
/// 5. Add the generated route to the [routes] list:
///
/// ```dart
/// @override
/// List<AutoRoute> get routes => [
///       DefaultRoute(
///         page: ThemeDevelopmentRoute.page,
///         path: '/theme-development',
///       ),
///       DefaultRoute(
///         page: MyRoute.page, // <-- this is the generated route based off of [MyScreen]
///         path: '/my-route', // the path of the route
///       ),
///     ];
/// ```
///
/// 6. If you want the rote to appear in the sidebar, see [Sidebar].
///
/// 7. For subroutes, just do the same and for the path use `parent-path/subroute-path`:
///
/// ```dart
/// @override
/// List<AutoRoute> get routes => [
///       DefaultRoute(
///         page: ThemeDevelopmentRoute.page,
///         path: '/theme-development',
///       ),
///       DefaultRoute(
///         page: MyRoute.page, // <-- this is the generated route based on [MyScreen]
///         path: '/theme-development/my-route', // in this example the parent path is '/theme-development', so [MyRoute] is a subroute of [ThemeDevelopmentRoute]
///       ),
///     ];
/// ```
///
/// This is particularly useful when using the [Sidebar]. Consider having a main route like /admin accessible from the [Sidebar], and under this main route, there might be additional pages not directly linked in the sidebar—subroutes. These subroutes, like /admin/sub-page, can be accessed through the main /admin route. To maintain a consistent navigation experience, the sidebar should highlight the admin item as active, even when navigating its subroutes.
@AutoRouterConfig(replaceInRouteName: 'Screen,Route')
class AppRouter extends _$AppRouter {
  @override
  List<AutoRoute> get routes => [
        DefaultRoute(
          page: ThemeDevelopmentRoute.page,
          path: '/theme-development',
        ),
        DefaultRoute(
          page: ScheduleRoute.page,
          path: '/schedule',
          initial: true,
          title: (context, data) => "Slot Booking",
        )
      ];
}

/// Implements [CustomRoute] with some default settings.
class DefaultRoute extends CustomRoute {
  /// Implements [CustomRoute] with some default settings.
  DefaultRoute(
      {required super.page, required super.path, super.initial, super.title})
      : super(transitionsBuilder: TransitionsBuilders.noTransition);
}
