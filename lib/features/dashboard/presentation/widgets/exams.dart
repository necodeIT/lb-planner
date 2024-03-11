import 'package:flutter/material.dart';

/// Exams section of the dashboard route.
class DashboardExams extends StatefulWidget {
  /// Exams section of the dashboard route.
  const DashboardExams({Key? key}) : super(key: key);

  @override
  Widget build(context, t) {
    return Consumer(builder: (context, ref, _) {
      var modules = ref.watch(modulesProvider);

      var exams = <Module>[];

      for (var module in modules.values) {
        if (module.type != ModuleTypes.test) continue;

        if (module.deadline == null) continue;

        var diff = module.deadline!.difference(DateTime.now());

        // ignore: no-magic-number
        if (diff.inDays.isNegative || diff.inDays > 14) continue;

        exams.add(module);
      }

      return LpContainer(
        title: t.dashboard_exams_title,
        width: double.infinity,
        child: ConditionalWidget(
          condition: exams.isNotEmpty,
          trueWidget: (context) => ListView(
            children: [
              for (var exam in exams) ...[
                ModuleWidget.date(
                  moduleId: exam.id,
                ),
                NcSpacing.small(),
              ],
            ],
          ),
          falseWidget: (context) => Center(
            child: NcBodyText(t.dashboard_exams_noExams),
          ),
        ),
      );
    });
  }
}
