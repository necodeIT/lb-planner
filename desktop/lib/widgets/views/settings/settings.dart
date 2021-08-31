import 'package:desktop/widgets/views/dashboard/dashboard.dart';
import 'package:desktop/widgets/views/settings/theme.dart';
import 'package:flutter/material.dart';
import 'package:lb_planner/ui.dart';

class SettingView extends StatelessWidget {
  const SettingView({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return View(
      title: "Settings",
      content: Row(
        children: [
          Expanded(
            flex: 2,
            child: Column(
              children: [
                Expanded(
                  child: Row(
                    children: [
                      Expanded(
                        child: NcContainer(
                          label: NcCaptionText(
                            "General",
                            fontSize: Dashboard.titleSize,
                          ),
                          body: NcLoadingIndicator(),
                        ),
                      ),
                      NcSpacing.large(),
                      Expanded(
                        child: NcContainer(
                          label: NcCaptionText(
                            "Themes",
                            fontSize: Dashboard.titleSize,
                          ),
                          body: NcGridView(
                            spacing: NcSpacing.xlSpacing,
                            children: [
                              for (var theme in NcThemes.all.keys) ThemeItem(theme: theme),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                NcSpacing.large(),
                Expanded(
                  flex: 2,
                  child: NcContainer(
                    label: NcCaptionText(
                      "Courses",
                      fontSize: Dashboard.titleSize,
                    ),
                    body: NcLoadingIndicator(),
                  ),
                )
              ],
            ),
          ),
          NcSpacing.large(),
          Expanded(
            child: NcContainer(
              label: NcCaptionText(
                "Feedback",
                fontSize: Dashboard.titleSize,
              ),
              body: NcLoadingIndicator(),
            ),
          ),
        ],
      ),
    );
  }
}
