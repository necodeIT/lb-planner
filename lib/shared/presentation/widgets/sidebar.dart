import 'package:animations/animations.dart';
import 'package:auto_route/auto_route.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import 'package:lb_planner/features/auth/auth.dart';
import 'package:lb_planner/shared/shared.dart';

/// Wraps the current route in a sidebar for navigation.
///
/// Do not use this widget directly, instead use [SidebarWrapperMixin].
///
/// In order to add a route to the sidebar, do the following:
///
/// 1. Follow the steps in [AppRouter] until step 6.
///
/// 2. If you want to add the route at the top of the sidebar, add it to the [Column] with the comment "Top items" using [SidebarItem].
///
/// 3. If you want to add the route at the bottom of the sidebar, add it to the [Column] with the comment "Footer items" using [SidebarItem].
class Sidebar extends ConsumerWidget {
  /// Wraps the current route in a sidebar for navigation.
  const Sidebar({super.key, required this.body});

  /// The body of the currently active page.
  final Widget body;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final user = ref.watch(userProvider);

    return Material(
      child: Container(
        color: Theme.of(context).colorScheme.background,
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.symmetric(vertical: 20),
              decoration: BoxDecoration(
                color: Theme.of(context).colorScheme.surface,
                boxShadow: [
                  BoxShadow(
                    color: Theme.of(context)
                        .colorScheme
                        .onSurface
                        .withOpacity(0.1),
                    blurRadius: 10,
                    offset: Offset(0, 0),
                  ),
                ],
              ),
              width: 60,
              child: Column(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  // Top items
                  Column(
                    children: [
                      SidebarItem(
                        icon: Icons.dashboard,
                        // TODO: swap with dashboard route once implemented
                        route: ThemeDevelopmentRoute(),
                      ),
                      SidebarItem(
                        icon: Icons.book_online,
                        route: ScheduleRoute(),
                      ),
                      SizedBox(height: 10),
                      SidebarItem(
                        icon: Icons.calendar_month,
                        // TODO: swap with calendar route once implemented
                        route: ThemeDevelopmentRoute(),
                      ),
                    ],
                  ),

                  // Footer items
                  Column(
                    children: [
                      if (kDebugMode)
                        SidebarItem(
                          route: ThemeDevelopmentRoute(),
                          icon: FontAwesome.paint_brush,
                        ),
                      SizedBox(height: 10),
                      if (user?.isElevated ?? false)
                        SidebarItem(
                          icon: Icons.admin_panel_settings,
                          // TODO: swap with admin route once implemented
                          route: ThemeDevelopmentRoute(),
                        ),
                      if (user?.isElevated ?? false) SizedBox(height: 10),
                      SidebarItem(
                        icon: Icons.settings,
                        // TODO: swap with settings route once implemented
                        route: ThemeDevelopmentRoute(),
                      ),
                      SizedBox(height: 10),
                      SidebarItem(
                        // TODO: swap with login route once implemented
                        route: ThemeDevelopmentRoute(),
                        icon: Icons.logout,
                        // TODO: onTap: logout the user,
                      )
                    ],
                  ),
                ],
              ),
            ),
            Expanded(
              child: Padding(
                padding: const EdgeInsets.all(30),
                child: Column(
                  children: [
                    ScreenTitleBar(),
                    SizedBox(height: 20),
                    Builder(
                      builder: (context) {
                        return Expanded(
                          child: PageTransitionSwitcher(
                            duration: Duration(milliseconds: 500),
                            transitionBuilder:
                                (child, animation, secondaryAnimation) =>
                                    SharedAxisTransition(
                              fillColor:
                                  Theme.of(context).colorScheme.background,
                              animation: animation,
                              secondaryAnimation: secondaryAnimation,
                              transitionType: SharedAxisTransitionType.vertical,
                              child: child,
                            ),
                            child: SizedBox(
                              key: ValueKey(context.router.current.path),
                              child: body,
                            ),
                          ),
                        );
                      },
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

/// Auto wraps a [Widget] with a [Sidebar].
///
/// Usage:
///
/// **StatelessWidgets**
///
/// ```dart
/// class MyWidget extends StatelessWidget with SidebarWrapperMixin {
///   const MyWidget({super.key});
///
///   @override
///   Widget build(BuildContext context) {
///       return Container();
///   }
/// }
/// ```
///
/// **StatefulWidgets**
///
/// ```dart
/// class MyWidget extends StatefulWidget with SidebarWrapperMixin {
///   const MyWidget({super.key});
///
///  @override
/// _MyWidgetState createState() => _MyWidgetState();
/// }
///
/// class _MyWidgetState extends State<MyWidget> {
///   @override
///   Widget build(BuildContext context) {
///      return Container();
///  }
/// }
/// ```
mixin SidebarWrapperMixin on Widget implements AutoRouteWrapper {
  @override
  Widget wrappedRoute(BuildContext context) {
    return Sidebar(
      body: this,
    );
  }
}
