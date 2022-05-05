part of lbplanner_routes;

/// Month grid for the calendar.
class CalendarPlanMonth extends StatelessWidget {
  /// Month grid for the calendar.
  CalendarPlanMonth({Key? key, required this.month}) : super(key: key);

  /// The month to display.
  final DateTime month;

  /// Formatter for the weekdays.
  final formatter = DateFormat("EEE");

  @override
  Widget build(BuildContext context) {
    /// Get all the days in the month.

    var days = List<DateTime>.generate(
      DateTime(month.year, month.month + 1, 0).day,
      (i) => DateTime(month.year, month.month, i + 1),
    );

    /// a map of all days of each week.
    var weeks = Map<int, List<DateTime>>();

    for (var day in days) {
      if (!weeks.containsKey(day.weekday)) {
        weeks[day.weekday] = [];
      }

      weeks[day.weekday]!.add(day);
    }

    /// get the days of the next month.
    var nextMonthDays = List<DateTime>.generate(
      DateTime(month.year, month.month + 1, 0).day,
      (i) => DateTime(month.year, month.month + 1, i + 1),
    );

    /// fill in the days of the next month.

    var missingDays = 7 - weeks.values.last.length;

    /// I don't know why this is necessary. Just don't touch it.
    missingDays++;

    for (var day in nextMonthDays.take(missingDays)) {
      weeks[day.weekday]!.add(day);
    }

    return Row(
      children: [
        for (var i in weeks.keys) ...[
          Expanded(
            child: Column(
              children: [
                NcSpacing.xs(),
                Center(
                  child: NcTitleText(
                    formatter.format(DateTime(0, 0, i)),
                  ),
                ),
                NcSpacing.xs(),
                for (var day in weeks[i]!) ...[
                  Expanded(child: CalendarPlanCell(day: day, isCurrentMonth: day.month == month.month)),
                ],
              ],
            ),
          ),
        ],
      ],
    );
  }
}