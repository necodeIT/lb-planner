// GENERATED CODE - DO NOT MODIFY BY HAND

// **************************************************************************
// AutoRouterGenerator
// **************************************************************************

// ignore_for_file: type=lint
// coverage:ignore-file

part of 'app_route.dart';

abstract class _$AppRouter extends RootStackRouter {
  // ignore: unused_element
  _$AppRouter({super.navigatorKey});

  @override
  final Map<String, PageFactory> pagesMap = {
    ThemeDevelopmentRoute.name: (routeData) {
      return AutoRoutePage<dynamic>(
        routeData: routeData,
        child: const ThemeDevelopmentScreen(),
      );
    }
  };
}

/// generated route for
/// [ThemeDevelopmentScreen]
class ThemeDevelopmentRoute extends PageRouteInfo<void> {
  const ThemeDevelopmentRoute({List<PageRouteInfo>? children})
      : super(
          ThemeDevelopmentRoute.name,
          initialChildren: children,
        );

  static const String name = 'ThemeDevelopmentRoute';

  static const PageInfo<void> page = PageInfo<void>(name);
}
