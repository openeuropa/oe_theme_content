
# OpenEuropa Content Consultation companion module

This module is a theming companion module to the [OpenEuropa Content Consultation](https://github.com/openeuropa/oe_content/tree/master/modules/oe_content_consultation) module.
It provides the logic needed to theme the Consultation content type.

## Installation

Make sure you have read the OpenEuropa Content Consultation module's [README.md](https://github.com/openeuropa/oe_content/blob/master/modules/oe_content_consultation/README.md)
before enabling this module.

## Required contrib modules

This module requires the following contributed modules:

* [Extra field](https://www.drupal.org/project/extra_field) (^1.1)
* [Field group](https://www.drupal.org/project/field_group) (~3.0)
* [Rdf skos](https://github.com/openeuropa/rdf_skos) (~0.11)

## Shipped configuration

The module ships the following date formats:

* Consultation date, e.g. `25 July 2020`
* Consultation date with time and timezone, e.g. `07 August 2021, 09:15 (CEST)`

## Overridden configuration

Installing this module will create the `full` view mode of "Consultation" content type in order to guarantee that fields
and formatter settings are displayed correctly.

If you want to customize how the "Consultation" looks like override the `full` view mode.

This modules also ships with a `teaser` view mode.

## Extra fields

This module ships with [extra fields](https://www.drupal.org/project/extra_field) plugin definition which are
used to display complex rendering business logic. All this logic is encapsulated in these extra field.

You can reuse these extra fields in your own view modes.

List of Extra field definitions:

* [Consultation status](modules/oe_theme_content_consultation/src/Plugin/ExtraField/Display/ConsultationStatusExtraField.php):
  provides the consultation status, depending on the current time in relation with consultation's opening/closing dates.
* [Consultation status label](modules/oe_theme_content_consultation/src/Plugin/ExtraField/Display/ConsultationLabelStatusExtraField.php):
  same as above, only showed using ECL label component, and prefixed by "Status:".
* [Closed status text](modules/oe_theme_content_consultation/src/Plugin/ExtraField/Display/ConsultationClosedTextExtraField.php):
  provides the consultation closed status text with default value when there is no text available.

