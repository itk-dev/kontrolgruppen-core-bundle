# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.6.4] - 2020-05-13
### Fixed
- [KON-289](https://jira.itkdev.dk/browse/KON-289): Fixed missing name on existing users

## [1.6.3] - 2020-05-06
### Added
- [Changelog](https://github.com/aakb/kontrolgruppen-core-bundle/pull/177): Added CHANGELOG file
- [KON-23](https://jira.itkdev.dk/browse/KON-23): Delete old processes
- [KON-289](https://jira.itkdev.dk/browse/KON-289): Show name of user when hovering AZ

### Changed
- [KON-362](https://github.com/aakb/kontrolgruppen-core-bundle/pull/184): Include non-revenue cases in KL report
- [KON-354](https://github.com/aakb/kontrolgruppen-core-bundle/pull/187): Changed BI fields
- [KON-360](https://github.com/aakb/kontrolgruppen-core-bundle/pull/185): Adjusted report
- [KON-361](https://github.com/aakb/kontrolgruppen-core-bundle/pull/188): Changed datetime picker (sammen med https://github.com/aakb/kontrolgruppen-core-bundle/pull/189)

### Fixed
- [KON-362](https://github.com/aakb/kontrolgruppen-core-bundle/pull/183): Fixed annotation
- [KON-359](https://github.com/aakb/kontrolgruppen-core-bundle/pull/186): Added null check

## [1.6.1] - 2020-03-13
### Changed
- [KON-352](https://jira.itkdev.dk/browse/KON-352): Adjustments to report

## [1.6.0] - 2020-03-09
### Added
- [KON-350](https://jira.itkdev.dk/browse/KON-350): Client postal code added in BI export

## [1.5.0] - 2020-02-27
### Changed
- [KON-332](https://jira.itkdev.dk/browse/KON-332): New revenue form and calculation.

### Fixed
- [KON-348](https://jira.itkdev.dk/browse/KON-348): Middle name fix in CPR service
- [KON-304](https://jira.itkdev.dk/browse/KON-319): Fixed search params to include CPR with and without dashes.


## [1.4.1] - 2020-02-13
### Changed
- [KON-347](https://jira.itkdev.dk/browse/KON-347): Replaced first name and last name search parameters with full name search parameter.
- [KON-320](https://jira.itkdev.dk/browse/KON-320): Non assigned processes are shown at the bottom on dashboard page. Number of processes shown on the dashboard is user specific.
- [KON-345](https://jira.itkdev.dk/browse/KON-320): Result with unknown addresses from CPR service is now handled.

### Fixed
- [KON-321](https://jira.itkdev.dk/browse/KON-321): Fixed default sorting of processes on process index page to descending order.
