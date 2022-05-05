part of lbplanner_routes;

/// Select courses route diplayed when the user has no courses selected.
class LoginSelectCoursesRoute extends LocalizedWidget {
  /// Select courses route diplayed when the user has no courses selected.
  const LoginSelectCoursesRoute({Key? key}) : super(key: key);

  /// The name of this route.
  static const routeName = '/login/select-courses';

  /// The margin of the selectioon form.
  static const double margin = 100.0;

  @override
  Widget create(context, t) {
    return Stack(
      children: [
        Positioned(
          top: NcSpacing.largeSpacing,
          left: NcSpacing.largeSpacing,
          child: NcCaptionText(
            t.login_selectCourses_title,
            fontSize: RouteTitle.titleSize,
          ),
        ),
        Align(
          alignment: Alignment.centerLeft,
          child: NcVectorImage(
            code: svg_select_courses,
            height: MediaQuery.of(context).size.height * .7,
            alignment: Alignment.centerLeft,
          ),
        ),
        Align(
          alignment: Alignment.centerRight,
          child: Padding(
            padding: const EdgeInsets.only(right: margin),
            child: LoginSelectCourseCourseSelection(),
          ),
        ),
      ],
    );
  }
}