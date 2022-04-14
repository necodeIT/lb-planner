// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'feedback.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

_$_Feedback _$$_FeedbackFromJson(Map<String, dynamic> json) => _$_Feedback(
      id: json['id'] as int,
      userId: json['userId'] as int,
      content: json['content'] as String,
      notes: json['notes'] as String? ?? null,
      type: $enumDecode(_$FeedbackTypesEnumMap, json['type']),
      status: $enumDecode(_$FeedbackStatusEnumMap, json['status']),
    );

Map<String, dynamic> _$$_FeedbackToJson(_$_Feedback instance) =>
    <String, dynamic>{
      'id': instance.id,
      'userId': instance.userId,
      'content': instance.content,
      'notes': instance.notes,
      'type': _$FeedbackTypesEnumMap[instance.type],
      'status': _$FeedbackStatusEnumMap[instance.status],
    };

const _$FeedbackTypesEnumMap = {
  FeedbackTypes.suggestion: 'suggestion',
  FeedbackTypes.bug: 'bug',
  FeedbackTypes.error: 'error',
  FeedbackTypes.other: 'other',
};

const _$FeedbackStatusEnumMap = {
  FeedbackStatus.unread: 'unread',
  FeedbackStatus.read: 'read',
};