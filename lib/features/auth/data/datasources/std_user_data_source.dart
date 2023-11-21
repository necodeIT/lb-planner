import 'package:lb_planner/features/auth/auth.dart';
import 'package:lb_planner/shared/shared.dart';

/// Standard implementation of [UserDataSource].
class StdUserDataSource extends UserDataSource {
  /// The token to use for authentication.
  final UserToken userToken;

  /// The service to use for API calls.
  final ApiService apiService;

  /// Standard implementation of [UserDataSource].
  StdUserDataSource(this.apiService, this.userToken);

  @override
  Future<List<User>> fetchAllUsers() async {
    final response = await apiService.callFunction(
      function: "local_lbplanner_user_get_all_users",
      token: userToken.lbPlannerApiToken,
      body: {},
    );

    if (response.failed) {
      throw Exception("Failed to fetch users");
    }

    return List.from(response.expectMultiple())
        .map((e) => User.fromJson(e))
        .toList();
  }

  @override
  Future<User> fetchCurrentUser() async {
    var response = await apiService.callFunction(
      function: "local_lbplanner_user_get_user",
      token: userToken.lbPlannerApiToken,
      body: {},
    );

    if (response.failed) {
      throw Exception("Failed to fetch current user");
    }

    return User.fromJson(response.expectSingle());
  }

  @override
  Future<User> updateUser(User user) async {
    var response = await apiService.callFunction(
      function: "local_lbplanner_user_update_user",
      token: userToken.lbPlannerApiToken,
      body: {
        "colorblindness": user.colorBlindnessString,
        "lang": user.language,
        "theme": user.themeName,
        "displaytaskcount": user.displayTaskCountInt,
      },
    );

    if (response.failed) {
      throw Exception("Failed to update user");
    }

    return User.fromJson(response.expectSingle());
  }

  @override
  Future<void> deleteUser(User user) async {
    var response = await apiService.callFunction(
      function: "local_lbplanner_user_delete_user",
      token: userToken.lbPlannerApiToken,
      body: {
        "userid": user.id,
      },
    );

    if (response.isNotOk) {
      throw Exception("Failed to delete user");
    }
  }
}
