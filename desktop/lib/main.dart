import 'dart:io';
import 'package:desktop/home.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:lb_planner/data.dart';
import 'package:lb_planner/ui.dart';
import 'package:window_size/window_size.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  NcThemes.current = NcThemes.all[User.current.settings.theme] ?? NcThemes.dark;
  // NcThemes.current = NcThemes.light;
  if (Platform.isWindows || Platform.isLinux || Platform.isMacOS) {
    setWindowMinSize(const Size(1550.0, 850.0));
    setWindowMaxSize(Size.infinite);
  }
  runApp(app);
}

final app = MaterialApp(
  home: App(),
  title: "LB Planner",
  scrollBehavior: NcScrollBehavior(),
);

class App extends StatefulWidget {
  App({Key? key}) : super(key: key);

  @override
  _AppState createState() => _AppState();
}

class _AppState extends State<App> {
  @override
  void initState() {
    super.initState();
    NcThemes.onCurrentThemeChange = () =>
        setState(() => User.current.settings.theme = NcThemes.current.name);

    Guard.init(context);

    Guard.checkForRecentCrash(context);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: NcThemes.current.secondaryColor,
      body: Home(),
    );
  }
}
