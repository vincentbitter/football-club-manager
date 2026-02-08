# Changelog

All notable changes to this project will be documented in this file.

## [0.11.0] - 2026-02-08

### 🐛 Bug Fixes

- Add missing NL translations
- Add missing translations in editor

### 🚜 Refactor

- Load settings class from plugin file

## [0.10.0] - 2026-02-08

### 🚀 Features

- Compatible with WordPress 6.9
- Date of birth for players, including privacy settings
- Birthdays block showing today's birthdays
- Volunteers management

## [0.9.0] - 2025-11-25

### 🐛 Bug Fixes

- Show preview content in block editor.

### 📚 Documentation

- Improve readme.

### 🎨 Styling

- Support mobile/tablet view for match results and schedule blocks.

## [0.8.0] - 2025-11-19

### 🚀 Features

- Results block showing recent matches.
- Schedule block showing upcoming matches.

### 🐛 Bug Fixes

- Sort matches by time instead of date only.
- Only show results when scores are available.
- Show labels in block editor for showing elements on team page.
- Security findings by plugin check.

## [0.7.0] - 2025-11-03

### 🚀 Features

- Upcoming matches component.
- Match results component.
- Add upcoming matches and recent results to team page.

## [0.6.0] - 2025-08-17

### 🚀 Features

- Team Players block on team page and in Block Editor.

### 🚜 Refactor

- Move assets to public folder.

### ⚙️  Miscellaneous Tasks

- Lower minimum PHP version requirement to 7.4.
- Use wp-scripts plugin-zip to create release file.

## [0.5.0] - 2025-08-17

### 🚀 Features

- Release to Wordpress.org Plugin Directory.

## [0.4.0] - 2025-08-08

### 🐛 Bug Fixes

- Mistake in Dutch translations.

### 🚜 Refactor

- Use unique function names to avoid conflicts with other plugins.

## [0.3.0] - 2025-07-17

### 🐛 Bug Fixes

- Nonce validation error on creating new players or matches.

## [0.2.0] - 2025-07-16

### 🚀 Features

- Allow bulk import tools to skip meta box handling.

### 🐛 Bug Fixes

- Zip-file renamed to football-club-manager.zip (without version number), so plugin will end in the correct folder.
- Required version set to 6.8 (major) instead of 6.8.1.
- Limit handling meta box updates to specific post types.
- *(security)* Improve nonce validation logic in match and player meta boxes.
- Remove .editorconfig from release.

### 🚜 Refactor

- Use own dashboard code instead of depending on core dashboard.php.
- Prefix renamed from cfm to cfmanager, to avoid conflicts with other plugins.

### ⚙️  Miscellaneous Tasks

- Editor config file added for consistent coding

## [0.1.0] - 2025-07-03

### 📚 Documentation

- Create changelog

### ⚙️  Miscellaneous Tasks

- Create GitHub release


