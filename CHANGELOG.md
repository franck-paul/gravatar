# Changelog

## 7.1 — 2026-11-07

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 7.0.1 — 2026-09-27

- Fix avatar management when using libravatar
- Use Html/Form/Note rather than Para when it is applicable
- Enforces some verification before registering module

## 7.0 — 2026-08-03

- Avoid using magic methods for user preferences and blog settings (2.39+)
- Use MetaRecord::strField(), MetaRecord::intField() and MetaRecord::boolField() whenever is possible

## 6.2 — 2026-04-07

- DA -&gt; DC

## 6.1 — 2025-09-22

- Use backend services rather than static calls

## 6.0 — 2025-09-07

- DC 2.36 compliance

## 5.1 — 2025-08-08

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 5.0 — 2025-03-31

- Use TemplateHelper for template blocks/values DC 2.34+

## 4.2.1 — 2025-01-09

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 4.2 — 2024-09-04

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 4.1 — 2023-10-27

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 4.0 — 2023-10-25

- Use instanced item constants rather than interface ones
- 2.28 compliance

## 3.0 — 2023-08-25

- Dotclear 2.27 compliance, PHP 8.1+

## 2.1.1 — 2023-06-27

- Add local static $init variable

## 2.1 — 2023-06-20

- Use dcCore::app()-&gt;adminurl-&gt;redirect() rather than Http::redirect()

## 2.0.1 — 2023-06-12

- Don't do things in frontend if plugin disabled

## 2.0 — 2023-06-10

- Switch to namespace
- Prepare 2.26

## 1.1 — 2022-12-18

- 2.24 compliance

## 1.0.1 — 2022-11-30

- Avoid notice on new installation

## 1.0 — 2022-11-27

- Dotclear 2.24 compliance

## 0.10.1 — 2022-08-14

- Usual oups :-)

## 0.10 — 2022-08-13

- Update default branch in _define.php for alternative repository
- Add alternative repository

## 0.9 — 2020-02-25

- Add support and details URLs
- Add load="lazy" to gravatar images
- short notation for array (array() → [])

## 0.8 — 2018-01-23

- Cope with unresponding DNS
- Fix : use closure for priority sorting
- Add DNS federation lookup for Libravatar, remove automatic display for trackbacks/pingbacks/webmentions

## 0.7 — 2018-01-22

- Core 2.13 dependencies in order to require PHP 5.5+
- Add libravatar.org alternative service

## 0.6 — 2016-10-28

- Using dcPage::cssLoad, dcPage::jsLoad and dcPage::getPF is a better way to load plugin resources

## 0.5 — 2015-01-28

- Add width and height in &lt;img&gt; tag
- Remove notices
- Init for non default blog

## 0.4 — 2013-10-04

- new _define.php
- New notices system
- Cope with new breadcrumb
- No more $GLOBALS at top level, and various
- various (next release will 0.4)
- Cosmetic fix

## 0.3 — 2013-03-16

- Version 0.3
- harmonize &lt;label&gt;&lt;/label&gt; in forms
- Now uses core message function (dcPage::message)
- Add missing files
- Revert to original name and desc in define.php of plugins. Localized versions are not required here.

## 0.2.1 — 2012-05-28

- Version 0.2.1
- Echappement des URLs (thank's brol)

## 0.2 — 2012-05-06

- Ajout balises templates

## 0.1 — 2011-08-01

- Modification libellés
- Rajout lien vers le site Gravatar sur la page de config
- Traductions française et icône
- Fixes and CSS style option
- Ajout admin
- Minor fixes
- Premier commit
