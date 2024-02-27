import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:lb_planner/shared/shared.dart';
import '../../../auth/domain/providers/providers.dart';

/// Themes settings theme item.
class SettingsThemesThemeItem extends StatefulWidget {
  /// Themes settings theme item.
  const SettingsThemesThemeItem({Key? key, required this.theme})
      : super(key: key);

  /// The theme to dislplay.
  final Theme theme;

  /// The radius of the corners.
  static const double radius = 14;

  /// The size of the icon.
  static const double iconSize = 22;

  /// The width of the border.
  static const double borderWidth = 2;

  /// The size of the item.
  static const double size = 48;

  @override
  State<SettingsThemesThemeItem> createState() =>
      _SettingsThemesThemeItemState();
}

class _SettingsThemesThemeItemState extends State<SettingsThemesThemeItem> {
  Future<RawApiResponse>? _themeFuture;

  _setTheme(WidgetRef ref) async {
    if (widget.theme == Theme.current) return;

    var controller = ref.read(userController);

    // throw Exception('Catgirl');

    _themeFuture = null;

    setState(() {
      _themeFuture = controller.updateTheme(widget.theme.name);
    });

    var response = await _themeFuture!;

    if (response.succeeded) {
      var user = ref.read(userProvider);

      applyUserTheme(user);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Consumer(
      builder: (context, ref, _) => HoverBuilder(
        onTap: () => _setTheme(ref),
        builder: (context, hovering) {
          var active = widget.theme == NcThemes.current || hovering;

          return AnimatedContainer(
            padding: EdgeInsets.all(
                active ? 0 : SettingsThemesThemeItem.borderWidth),
            height: SettingsThemesThemeItem.size,
            width: SettingsThemesThemeItem.size,
            duration: Duration(milliseconds: 100),
            curve: Curves.easeOutCubic,
            decoration: BoxDecoration(
              color: widget.theme.primaryColor.applyColorBlindness(),
              border: Border.all(
                color: active
                    ? context.theme.colorScheme.primary
                    : widget.theme.primaryColor.applyColorBlindness(),
                width: active ? SettingsThemesThemeItem.borderWidth : 0,
              ),
              boxShadow: kElevationToShadow[4],
              borderRadius:
                  BorderRadius.circular(SettingsThemesThemeItem.radius),
            ),
            child: ConditionalWrapper(
              condition: _themeFuture != null,
              wrapper: (context, child) => FutureBuilder(
                future: _themeFuture,
                builder: (context, snapshot) => ConditionalWidget(
                  condition: snapshot.connectionState == ConnectionState.done,
                  ifTrue: child,
                  ifFalse: CircularProgressIndicator(
                    color: context.theme.colorScheme.primary,
                    strokeWidth: 3,
                  ),
                ),
              ),
              child: Icon(
                widget.theme.icon,
                size: SettingsThemesThemeItem.iconSize,
                color: widget.theme.iconColor.applyColorBlindness(),
              ),
            ),
          );
        },
      ),
    );
  }
}
