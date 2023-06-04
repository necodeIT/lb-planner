// Package imports:

// Dart imports:
import 'dart:ui';

// Package imports:
import 'package:freezed_annotation/freezed_annotation.dart';

part 'course.freezed.dart';
part 'course.g.dart';

@freezed

/// Course model
class Course with _$Course {
  const Course._();

  /// Course model
  const factory Course({
    /// The id of the course.
    required int id,

    /// The name of the course.
    required String name,

    /// The short name of the course.
    required String shortname,

    /// The color of the course.
    required String colorCode,

    /// If the course is enabled or not.
    required bool enabled,
  }) = _Course;

  /// The color of the course.
  Color get color => Color(int.parse(colorCode.replaceFirst('#', '0xFF')));

  /// Course model from json
  factory Course.fromJson(Map<String, dynamic> json) => _$CourseFromJson(json);
}