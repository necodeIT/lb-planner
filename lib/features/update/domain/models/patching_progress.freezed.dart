// coverage:ignore-file
// GENERATED CODE - DO NOT MODIFY BY HAND
// ignore_for_file: type=lint
// ignore_for_file: unused_element, deprecated_member_use, deprecated_member_use_from_same_package, use_function_type_syntax_for_parameters, unnecessary_const, avoid_init_to_null, invalid_override_different_default_values_named, prefer_expression_function_bodies, annotate_overrides, invalid_annotation_target, unnecessary_question_mark

part of 'patching_progress.dart';

// **************************************************************************
// FreezedGenerator
// **************************************************************************

T _$identity<T>(T value) => value;

final _privateConstructorUsedError = UnsupportedError(
    'It seems like you constructed your class using `MyClass._()`. This constructor is only meant to be used by freezed and you are not supposed to need it nor use it.\nPlease check the documentation here for more information: https://github.com/rrousselGit/freezed#custom-getters-and-methods');

/// @nodoc
mixin _$PatchingProgress {
  /// The release that is currently being patched.
  Release get release => throw _privateConstructorUsedError;

  /// The progress of the patching process.
  double get progress => throw _privateConstructorUsedError;

  @JsonKey(ignore: true)
  $PatchingProgressCopyWith<PatchingProgress> get copyWith =>
      throw _privateConstructorUsedError;
}

/// @nodoc
abstract class $PatchingProgressCopyWith<$Res> {
  factory $PatchingProgressCopyWith(
          PatchingProgress value, $Res Function(PatchingProgress) then) =
      _$PatchingProgressCopyWithImpl<$Res, PatchingProgress>;
  @useResult
  $Res call({Release release, double progress});

  $ReleaseCopyWith<$Res> get release;
}

/// @nodoc
class _$PatchingProgressCopyWithImpl<$Res, $Val extends PatchingProgress>
    implements $PatchingProgressCopyWith<$Res> {
  _$PatchingProgressCopyWithImpl(this._value, this._then);

  // ignore: unused_field
  final $Val _value;
  // ignore: unused_field
  final $Res Function($Val) _then;

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? release = null,
    Object? progress = null,
  }) {
    return _then(_value.copyWith(
      release: null == release
          ? _value.release
          : release // ignore: cast_nullable_to_non_nullable
              as Release,
      progress: null == progress
          ? _value.progress
          : progress // ignore: cast_nullable_to_non_nullable
              as double,
    ) as $Val);
  }

  @override
  @pragma('vm:prefer-inline')
  $ReleaseCopyWith<$Res> get release {
    return $ReleaseCopyWith<$Res>(_value.release, (value) {
      return _then(_value.copyWith(release: value) as $Val);
    });
  }
}

/// @nodoc
abstract class _$$_PatchingProgressCopyWith<$Res>
    implements $PatchingProgressCopyWith<$Res> {
  factory _$$_PatchingProgressCopyWith(
          _$_PatchingProgress value, $Res Function(_$_PatchingProgress) then) =
      __$$_PatchingProgressCopyWithImpl<$Res>;
  @override
  @useResult
  $Res call({Release release, double progress});

  @override
  $ReleaseCopyWith<$Res> get release;
}

/// @nodoc
class __$$_PatchingProgressCopyWithImpl<$Res>
    extends _$PatchingProgressCopyWithImpl<$Res, _$_PatchingProgress>
    implements _$$_PatchingProgressCopyWith<$Res> {
  __$$_PatchingProgressCopyWithImpl(
      _$_PatchingProgress _value, $Res Function(_$_PatchingProgress) _then)
      : super(_value, _then);

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? release = null,
    Object? progress = null,
  }) {
    return _then(_$_PatchingProgress(
      release: null == release
          ? _value.release
          : release // ignore: cast_nullable_to_non_nullable
              as Release,
      progress: null == progress
          ? _value.progress
          : progress // ignore: cast_nullable_to_non_nullable
              as double,
    ));
  }
}

/// @nodoc

class _$_PatchingProgress implements _PatchingProgress {
  _$_PatchingProgress({required this.release, required this.progress});

  /// The release that is currently being patched.
  @override
  final Release release;

  /// The progress of the patching process.
  @override
  final double progress;

  @override
  String toString() {
    return 'PatchingProgress(release: $release, progress: $progress)';
  }

  @override
  bool operator ==(dynamic other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _$_PatchingProgress &&
            (identical(other.release, release) || other.release == release) &&
            (identical(other.progress, progress) ||
                other.progress == progress));
  }

  @override
  int get hashCode => Object.hash(runtimeType, release, progress);

  @JsonKey(ignore: true)
  @override
  @pragma('vm:prefer-inline')
  _$$_PatchingProgressCopyWith<_$_PatchingProgress> get copyWith =>
      __$$_PatchingProgressCopyWithImpl<_$_PatchingProgress>(this, _$identity);
}

abstract class _PatchingProgress implements PatchingProgress {
  factory _PatchingProgress(
      {required final Release release,
      required final double progress}) = _$_PatchingProgress;

  @override

  /// The release that is currently being patched.
  Release get release;
  @override

  /// The progress of the patching process.
  double get progress;
  @override
  @JsonKey(ignore: true)
  _$$_PatchingProgressCopyWith<_$_PatchingProgress> get copyWith =>
      throw _privateConstructorUsedError;
}
