import 'package:logging/logging.dart';
import 'package:lb_planner/shared/shared.dart';

/// Base class for all services.
///
/// Provides a method for logging messages and errors.
abstract class Service implements ILoggable {
  /// THe name of the data source.
  final String _name;

  /// Base class for all services.
  ///
  /// Provides a method for logging messages and errors.
  const Service(this._name);

  Logger get _log => Logger("Service.$_name.$runtimeType");

  @override
  void log(Object message, [Object? error, StackTrace? stackTrace]) {
    if (error != null) {
      _log.warning(message, error, stackTrace);
      return;
    }

    _log.finer(message, error, stackTrace);
  }
}
