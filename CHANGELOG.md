# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [1.12.0] - In development
### Added
- New degov_social_media_settings module for social media access control.
- The field to control the display of the time in event node type.

### Fixed
- Reinstalls missing audio embedded view mode.
- Removed linking dependencies through variables from media modules. 
- Fixes broken slideshow of type 2.
- Displays the position field of a media contact.
- Use comma as decimal separator for file sizes.

### Changed
- Changed RSS feeds view from rendered entity to fields for better control.

## [1.11.0] - 06-11-2017
### Changed
- Removed deprecated degov_view_helper module from codebase.

### Fixed
- All the events should be displayed by default from today.

## [1.10.0] - 27-10-2017
### Added
- Added composer dependency on views_reference module to degov_paragraph_view_reference.

### Changed
- Move views helper functionality from degov_common to degov_paragraph_view_reference.
- Field header paragraphs is not required in any content type anymore.

### Fixed
- Iframe paragraph title now uses the same layout as other paragraphs.

## [1.9.0] - 23.10.17
### Added
- New field title has been added to media.
- Redirect module as a dependency to degov_pathauto.

### Changed
- Multi valued entity reference fields that have entity browser widget now have ability to sort items
  with media_browser Entity Browser.
- Scheduled updates field widget on nodes is now set to be a complex inline entity form.
- Caption fields on media are migrated to the new title field.

### Fixed
- Default permissions have been added for the degov_sitemap_settings module.
- Right sidebar paragraph block is now shown in the node preview mode.

## [1.8.0]
### Changed
- degov_views_helper functionality was transfered to degov_common module.
- Added control field for optional media download.
- Make field_image_caption for media (image) optional.

### Fixed
- Template suggestions now can be set from different modules for the same bundle of entity type.
- The image preview in Media reference paragraph preview mode is now not overlapping the edit buttons.
