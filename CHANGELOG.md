# craft-attendees Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.1.9 - 2023-01-27

### Added
- created a storage/logs/attendees.log file where all the data gets written to 
- created a slack buzzer where all the errors gets send to (notifications plugin)
- created a dedicated storage path for writing the CSV files
- written a console command thats able to delete CSV files older than 60 days (or days set by the option)

## 1.1.8 - 2023-01-26

### Fixed
- Fixed utf-8 encoding issue when saving the attendee

## 1.1.7 - 2022-08-12

### Fixed
- Fixed the html entities the organisation name

## 1.1.6 - 2022-06-17

### Fixed
- Return `null` when the URN couldn't be found on the Metaseed api

## 1.1.5 - 2022-05-23

### Fixed
- Fixed the `is_null` check on anonymous in the Attendees helper

### Changed
- Added null check on the title creation of the queue job

## 1.1.4 - 2022-05-16

### Fixed
- Fixed the priority schools filter on dashboard
  Fixed the export newsletter

## 1.1.3 - 2022-04-22

### Changed
- Allowed empty names in CSV's for import

## 1.1.2.1 - 2022-04-22

### Fixed
- Added build files

## 1.1.2 - 2022-04-22

### Fixed
- Fixed labelling and description text for anonymous users

## 1.1.1 - 2022-04-11

### Fixed
- Sanitising queue job names
- Styling issues after craft update
- Job Role error on form edit

## 1.1.0 - 2022-04-11

### Added
- Added the feature to show events based on event type
- Added the feature to export events based on event type
- Engagement data on the trainings

### Changed
- Updated the dependencies
- Provided code clean up

### Fixed
- Fixed an issue where the dates wouldn't order correctly
- Fixed an issue where the last event date wouldn't always display correctly

## 1.0.8.13 - 2022-02-21
### Fixed
- Fixed the school data SQL export bug

## 1.0.8.14 - 2022-02-22
### Feature
- Performance updates on school data export

## 1.0.8.13 - 2022-02-21
### Fixed
- Fixed the school data export bug

## 1.0.8.12 - 2022-02-21
### Changed
- Bumped version for packagist

## 1.0.8.11 - 2022-02-21
### Changed
- Updated the schoole export CSV

## 1.0.9 - 2022-02-21
### Fix
- Metaseed urn removed NULL

## 1.0.8 - 2022-02-18
### Fix
- Update dashboard event fetching and sorting through Twig

- Fixed the dashboard calculations

### Added
- Export functionality

### Changed
- Updated the speed of the dashboard

## 1.0.7 - 2022-02-04

### Added
- Added data sanitation
- Added functionality to remove whitespace on emails

### Changed
- Average count formula for priority schools
- Logs now need to be reloaded manually

### Fix
- Fixed import migrations

## 1.0.6 - 2022-01-31
### Fix
- Fixed the date not updating after selecting a different period

## 1.0.4 - 2022-01-28
### Fix
- Fixed the date not updating after selecting a different period

### Changed
- Composer update

## 1.0.5 - 2022-01-28
### Fix
- Update dashboard event fetching and sorting through Twig

## 1.0.4 - 2022-01-28
### Fix
- Fixed the events query

## 1.0.3 - 2022-01-21
### Added
- Added the dashboard view

## 1.0.2 - 2022-01-21
### Added
- Migration to add a priority school

## 1.0.1 - 2022-01-14
### Added
- Migration to add a priority school

## 1.0.0 - 2021-12-09
### Added
- Initial release to create and import attendees for a specific training.
