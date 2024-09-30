# LB Planner

LB Planner is an application designed to help students plan and organize their self-taught studying by tracking and managing their modules. It provides an easy-to-use interface that allows users to stay on top of their academic goals. For more information, please visit our [website](https://projekte.tgm.ac.at/lb-planner/).

> [!IMPORTANT]  
> This repository has been split into seperate repos:
> - [App Repository](https://github.com/necodeIT/lb_planner_app)
> - [Plugin Repository](https://github.com/necodeIT/lb_planner_plugin)
> - [Docs Repository](https://github.com/necodeIT/lb_planner_docs)

## Compile Instructions

1. Clone the repository to your local machine.
2. Ensure that you have [FVM](https://fvm.app/docs/getting_started/installation/) installed on your machine.
3. Navigate to the project directory in the terminal.
4. Run `fvm use` to select the Flutter version.
5. Run `fvm flutter pub get` to install the required dependencies.
6. To build the application for release on macOS, run `fvm flutter build macos --release --no-sound-null-safety`. The built application can be found in the `build/macos/Build/Products/Release/` directory.
7. To build the application for release on Windows, run `fvm flutter build windows --release --no-sound-null-safety`. The built application can be found in the `build\windows\runner\Release\` directory.
8. To build the application for release on Linux, run `fvm flutter build linux --release --no-sound-null-safety`. The built application can be found in the `build/linux/release/bundle/` directory.

Note: LB Planner uses `--no-sound-null-safety` because some of the third-party packages used in the app do not yet support Flutter's null safety feature. If you encounter any issues while compiling or running the application, please refer to the official [Flutter documentation](https://flutter.dev/docs) or the [FVM documentation](https://fvm.app) for troubleshooting tips.

## Contributors
LB Planner was created by [McQuenji](https://github.com/bmceachnie), [Muhi](https://github.com/mkocagoel), [Riedler](https://github.com/RiedleroD), [Bishow B](https://github.com/officialbishowb) and [Can Polat](https://github.com/cpolat-tgm). We would like to extend our appreciation to the following external contributors for their contributions to the project:

- [Blobi blob](https://github.com/Blobii) for helping with the design
- [jszuecs-tgm](https://github.com/Jszuecs) for helping with documentation on the website
- [sproshut](https://github.com/sproshut) for "Workflow for automated build" ([#11](https://github.com/necodeIT/lb_planner/pull/11))
- [melli736](https://github.com/melli736) for helping with macOS testing & development
- [mariusthaler](https://github.com/mariusthaler) for macOS testing

If you would like to contribute to the project, please feel free to contact us at lb-planner@tgm.ac.at or create a pull request.

## License

LB Planner is licensed under the [Attribution-ShareAlike 4.0 International (CC BY-SA 4.0)](https://creativecommons.org/licenses/by-sa/4.0/) license.
