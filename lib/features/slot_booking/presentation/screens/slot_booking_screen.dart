import 'package:auto_route/annotations.dart';
import 'package:flutter/material.dart';
import 'package:lb_planner/features/slot_booking/presentation/widgets/timeslot_section.dart';

import '../../../../shared/presentation/widgets/sidebar.dart';

@RoutePage()
class ScheduleScreen extends StatelessWidget with SidebarWrapperMixin {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: ListView(
        children: [
          TimeSlotSection(timeSlot: '8:00-8:50'),
          TimeSlotSection(timeSlot: '8:50-9:40'),
          // Add more TimeSlotSections as needed
        ],
      ),
    );
  }
}
