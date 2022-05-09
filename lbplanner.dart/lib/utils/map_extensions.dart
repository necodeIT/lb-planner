part of lbplanner_api;

/// Provides utilities to remap parameters as the moodle api and the models have different names for their parameters.
extension ModelMappingExtensions on Map<String, dynamic> {
  /// Maps paramters to fit [User.fromJson].
  Map<String, dynamic> mapUser(String token) {
    var body = Map.of(this);

    body["language"] = this["lang"];

    var role = this["role"];
    if (role != null) body["accessLevel"] = UserAccessLevels.values[role].name;

    body["id"] = this["userid"];
    body["avatar"] = this["profileimageurl"];
    body["planId"] = this["planid"];
    body["token"] = token;
    body["colorBlindness"] = this["colorblindness"];

    return body;
  }

  /// Maps paramters to fit [Notifications.fromJson].
  Map<String, dynamic> mapNotification() {
    var body = Map.of(this);

    var payload = (this["info"] ?? "") as String;

    var type = this["type"];
    var status = this["status"];
    var id = this["notificationid"];

    body["timestamp"] = DateTime.fromMillisecondsSinceEpoch(this["timestamp"] * 1000).toString();

    try {
      body["payload"] = jsonDecode(payload.isEmpty ? '{}' : payload);
    } catch (e) {
      body["payload"] = {"value": payload};
    }

    body["type"] = NotificationTypes.values[type].name;
    body["status"] = NotificationStatus.values[status].name;
    body["id"] = id;

    return body;
  }

  /// Maps parameters to fit [Plan.fromJson]
  Map<String, dynamic> mapPlan() {
    var body = Map.of(this);

    body["id"] = this["planid"];
    body["ekEnabled"] = this["ekenabled"];
    List<Deadline> deadlines = [];
    for (var deadline in body["daedlines"]) {
      var deadlineMap = Map<String, dynamic>.of(deadline);
      deadlines.add(Deadline.fromJson(deadlineMap.mapDeadline()));
    }
    body["deadlines"] = deadlines;

    body["members"] = {for (var user in body["members"]) user["userid"]: PlanAccessTypes.values[user["accesstype"]]};

    return body;
  }

  /// Maps parameters to fit [Module.fromJson]
  Map<String, dynamic> mapModule() {
    var body = Map.of(this);

    body["id"] = this["moduleid"];
    body["courseId"] = this["courseid"];

    int gradeIndex = this["grade"] ?? -1;
    body["grade"] = gradeIndex.isNegative ? null : ModuleGrades.values[gradeIndex].name;

    int typeIndex = this["type"];
    body["type"] = ModuleTypes.values[typeIndex].name;

    int statusIndex = this["status"];
    body["status"] = ModuleStatus.values[statusIndex].name;

    int unixDeadline = this["deadline"];
    if(unixDeadline != null) body["deadline"] = DateTime.fromMillisecondsSinceEpoch(unixDeadline * 1000).toString();

    return body;
  }

  /// Maps parameters to fit [Feedback.fromJson]
  Map<String, dynamic> mapFeedback() {
    var body = Map.of(this);

    int typeIndex = this["type"];
    body["type"] = FeedbackTypes.values[typeIndex].name;

    int statusIndex = this["status"];
    body["status"] = FeedbackStatus.values[statusIndex].name;

    body["userId"] = this["userid"];
    body["comment"] = this["notes"];

    return body;
  }

  /// Maps parameters to fit [Course.fromJson]
  Map<String, dynamic> mapCourse() {
    var body = Map.of(this);

    body["id"] = this["courseid"];
    body["colorCode"] = this["color"];

    return body;
  }

  /// Maps parameters to fit [Deadline.fromJson]
  Map<String, dynamic> mapDeadline() {
    var body = Map.of(this);

    body["moduleId"] = this["moduleid"];
    body["start"] = DateTime.fromMillisecondsSinceEpoch(this["deadlinestart"] * 1000);
    body["end"] = DateTime.fromMillisecondsSinceEpoch(this["deadlineend"] * 1000);

    return body;
  }

  /// Maps parameters to fit [PlanInvite.fromJson]
  Map<String, dynamic> mapPlanInvite() {
    var body = Map.of(this);

    body["invitee"] = this["inviteeid"];
    body["inviter"] = this["inviterid"];
    body["planId"] = this["planid"];
    body["status"] = PlanInviteStatus.values[this["status"]].name;
    body["timeStamp"] = DateTime.fromMillisecondsSinceEpoch(this["timestamp"] * 1000);

    return body;
  }
}