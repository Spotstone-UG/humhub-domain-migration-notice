# Changelog

All notable changes are documented here.

## 0.1.3 — 2026-09-10

- Added an administrator action to reset all account-based display timing and
  one-week dismissals, making the notice eligible again for signed-in people.
- Documented that guest browser cookies cannot be cleared remotely.

## 0.1.2 — 2026-09-10

- Fixed host matching on installations with IDN support.
- Made the administrator preview render the complete current popup state,
  including countdown and visible actions, without recording display state.

## 0.1.1 — 2026-09-10

- Added a HumHub uninstall migration that removes the module table.
- Added English source messages and a German administrative translation.
- Made the entire countdown wording configurable.
- Added validation against an expired deadline and credential-bearing
  destination URLs.
- Improved keyboard handling, network-failure behaviour and small-screen
  scrolling in the notice dialog.
- Expanded isolated checks and the public documentation.

## 0.1.0 — 2026-09-10

- Initial release of Domain Migration Notice for HumHub 1.18.5.
