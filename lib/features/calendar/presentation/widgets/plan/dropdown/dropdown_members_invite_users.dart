import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import 'package:lb_planner/shared/shared.dart';

/// Invite dialog for inviting users to the plan.
class CalendarPlanDropDownInviteUsersDialog extends StatefulWidget {
  /// Invite dialog for inviting users to the plan.
  const CalendarPlanDropDownInviteUsersDialog({Key? key}) : super(key: key);

  /// The default font size used.
  static const fontSize = 18.0;

  @override
  State<CalendarPlanDropDownInviteUsersDialog> createState() =>
      _CalendarPlanDropDownInviteUsersDialogState();
}

class _CalendarPlanDropDownInviteUsersDialogState
    extends State<CalendarPlanDropDownInviteUsersDialog> {
  final TextEditingController _searchController = TextEditingController();

  @override
  initState() {
    super.initState();

    _searchController.addListener(() {
      setState(() {});
    });
  }

  @override
  Widget build(BuildContext context) {
    return Consumer(
      builder: (context, ref, _) {
        var allUsers = ref.watch(usersProvider);
        var user = ref.watch(userProvider);
        var users = allUsers.keys
            .where((id) => CalendarPlanDropDownMembers.filterSearch(
                id, allUsers, _searchController.text))
            .toList();

        return Column(
          children: [
            LpTextField.filled(
              controller: _searchController,
              placeholder: t.calendar_plan_dropdown_members_inviteUsers_search,
              fontSize: CalendarPlanDropDownInviteUsersDialog.fontSize,
              prefixIcon: Ionicons.search,
            ),
            Spacing.medium(),
            Expanded(
              child: ListView(
                controller: ScrollController(),
                children: [
                  for (var userId in users)
                    if (userId != user.id) ...[
                      CalendarPlanDropDownInviteUsersUser(userId: userId),
                      Spacing.small(),
                    ],
                ],
              ),
            ),
          ],
        );
      },
    );
  }
}
