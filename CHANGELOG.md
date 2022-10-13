# CHANGELOG

## Version 2.0.8

### Chore

-   Update dependencies + reference pdc-base plugin from BitBucket to GitHub

## Version 2.0.7

### Features

-   (refactor): sc feed pdc-type tax query, use all terms slugs except internal.

## Version 2.0.6

### Features

-   (feat): government type setting, used in feed.

## Version 2.0.5

### Features

-   (feat): remove internal pdc-items from sc feed.

## Version 2.0.4

### Features

-   (refactor): strip unnecessary characters in UPL values and display them in lowercase. Used in xml sc feed.

## Version 2.0.3

### Features

-   (feat): add UPL values in xml sc feed. UPL stands for 'Uniform Product List' and is used for naming the products and services of the Dutch government (https://standaarden.overheid.nl/upl)

## Version 2.0.2

### Fixed:

-   (fix): 'pdc-doelgroepen' terms in xml sc feed

## Version 2.0.1

### Fixed:

-   (fix): meta query used in creating xml feed

## Version 2.0.0

### Features:

-   (refactor): clean-up for version 1.0.
-   (feat): clean-up & improvement feed serviceprovider

### Added

-   (feat): ScItem model used in feed serviceprovider

### Changed

-   (refactor): architecture change in the pdc-base plug-in, used as dependency, affects namespaces used

## Version 1.2.2

-   (feat): add new audiences
-   (fix): tests
-   (feat): add php-cs-fixer
-   (feat): format documents

## Version 1.2.1

### Fix:

-   (fix): check if required file for `is_plugin_active` is already loaded, otherwise load it. Props @Jasper Heidebrink
