# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [1.15.1] - IN DEVELOPMENT
### Changes 
- Allow override of javascript behaviours in the theme.
- Calendar widget paging for events will only work as a pager not as a filter.

## [1.15.0] - 16-02-2018 
### Fixed
- Split the link in media preview into two parts, so the title.
  and description are highlighted separately.
- Month-Year translation in header calendar widget.
- Translated of German phrases into English.
- Remove quote in EU cookie module.
- Add list tag to paragraph download list and link list.
- Change the weight of date-range library so it could be overriden from the theme.

## [1.14.2] - 05-01-2018
### Fixed
- Removed duplicated colon in libraries.yml.

## [1.14.1] - 05-01-2018
### Fixed
- Simplenews newsletter plain text view modes.

## [1.14.0] - 20-12-2017 
### Added
- Added description for field field_slideshow_type in paragraph degov_paragraph_slideshow.
- Added support for media bundles tweet and instagram to degov_social_media_settings.

### Fixed
- Fixed configurations of degov_media_video configurations.

## Changed
- Chnage cardinility of simplenews_issue field to 10 in degov_simplenews module.
- Views reference when argument field is empty doesn't try to set the argument, 
  so the default argument is calculated by the view itself.

## [1.13.1] - 11-12-2017
### Added
- Updated dependencies from deGov modules to keep lightning up to date.

## [1.13.0] - 05-12-2017
### Added
- Media bundle facts (Module degov_media_facts).

### Removed
- The gallery subtitle field has been deprecated. In case your project
  used this field, than you must override all media gallery templates found
  in the degov_media_gallery module into your own theme and include the
  subtitle back into these templates.
  
## Changed
- Supported version of facets starts from 8.x-1.0-beta1.

### Fixes
- By default render the tags field with surrounding and inner item wrappers.
- Copied configuration from deprecated update_hook to install and rewrite.

## [1.12.3] - 24-11-2017
### Changed
- Remove tags display from document display.

## [1.12.0 - 1.12.2] - 24-11-2017
### Added
- New degov_social_media_settings module for social media access control.
- The field to control the display of the time in event node type.
- Validation for event dates.
- Public/internal title for all media bundles.
- Added novalidate attribute to node and media forms.
- Added entity reference fields to node type simplenews_issue.
- Filter of view modes on paragraphs edit form.
- Added Data protection checkbox to newsletter.
- A new media video preview image is added replacing the previous thumbnail.

### Changed
- Changed RSS feeds view from rendered entity to fields for better control.
- Remove untranslated nodes from sitemap.xml.
- Use default theme for sending HTML mails.
- By default the media search now shows 12 items and a full pager.
- Removes auto trimming of the teaser title.
- Updated editor role permissions.
- Media video template.
- Removed label and fixed info block for media video upload.

### Fixed
- Reinstalls missing audio embedded view mode.
- Removed linking dependencies through variables from media modules. 
- Fixes broken slideshow of type 2.
- Displays the position field of a media contact.
- Use comma as decimal separator for file sizes.
- Link image media tags to search page.
- Hide blog author if not specified in a blog article.
- Adapted minimum search keyword length to search index settings.

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
