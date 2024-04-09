import 'package:logging/logging.dart';
import 'package:lb_planner/shared/shared.dart';

/// Mixin for all repositories that provides a method for logging messages and errors.
///
/// All repositories should mixin this class.
///
/// ```dart
/// class MyRepository extends Notifier<T> with RepositoryMixin {
///  // ...
/// }
mixin RepositoryMixin implements ILoggable {
  Logger get _log => Logger("Repository.$runtimeType");

  @override
  void log(Object message, [Object? error, StackTrace? stackTrace]) {
    if (error != null) {
      _log.severe(message, error, stackTrace);
      return;
    }

    _log.info(message, error, stackTrace);
  }
}
