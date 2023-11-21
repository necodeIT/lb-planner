import 'dart:async';

import 'package:lb_planner/features/update/update.dart';
import 'package:riverpod/riverpod.dart';

/// Provides whether an update is available.
///
/// Resolves to `true` if an update is available, `false` otherwise.
class IsUpdateAvailableProviderState extends AsyncNotifier<bool> {
  /// The [ReleaseRepository] instance to use.
  late final ReleaseRepository releaseRepository;

  @override
  FutureOr<bool> build() {
    releaseRepository = ref.watch(releaseRepositoryProvider);

    return releaseRepository.isUpdateAvailable();
  }

  /// Checks whether an update is available.
  Future<void> checkForUpdates() async {
    state = AsyncLoading();

    state = await AsyncValue.guard(releaseRepository.isUpdateAvailable);
  }
}