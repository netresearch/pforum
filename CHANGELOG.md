# 5.0.1

## MISC

- 57dedff Apply php-cs-fixer rules
- e31e69f Update dev tools

## Contributors

- Rico Sonntag

# 5.0.0

## BUGFIX

- 330b8cd NASAFF-236: [BUGFIX] add l10n_source to TCA definition for tx_pformum_domain_model_forum. Fixes unknown column error on creating new forums.
- 086c6e0 [BUGFIX] Fix starttime/endtime TCA configuration
- 87f4a43 [BUGFIX] Fix toggling hidden state of records
- 1a3e257 [BUGFIX] Update TCA/Configuration to TYPO3 v11
- 0194167 [BUGFIX] The file-level docblock must follow the opening PHP tag in the file header
- 51cd95e [BUGFIX] Persist hidden state of post/topic
- 8788d11 [BUGFIX] Fix image title
- 2756407 [BUGFIX] Fix image upload fields of topic
- 145310f [BUGFIX] PHP Warning: Undefined array key "templateRootPath"
- b1141d2 [BUGFIX] Drop not required method type hint
- 548d140 [BUGFIX] Fix image upload fields, {object} is not required in "new"
- 99f49da [BUGFIX] Fix return type hint of getUser() method
- b208e68 [BUGFIX] make anonymousUser nullable
- 25b3d3f [BUGFIX] do not prepend sys_storage uid

## MISC

- 5b11e16 NASAFF-237: Add missing default values in TCA
- b62439a NASAFF-237: Add missing "l10n_source" to TCA definition
- 8999a4c NASAFF-226: Fix langauge file. Use NumerusWordViewHelper to display the number od posts.
- 5ca763d NASAFF-226: Add numerusWordViewHelper.
- 605da9e Move topic details into separate partial
- 4795857 Move breadcrumb into separate partial
- 6698daf Add method to query all not archived forums
- 4d18bf5 Sort post/topic records in backend by timestamp of latest change in descending order
- 47e6327 [NEW] Add option to set forum as archived (readonly rendered in FE)
- 54cbe19 [NEW] Use palettes in TCA
- 2f8950e Update code analysis tools and configuration
- 719f73d Update github CI workflow
- d0d9ea3 Use boolean values in TCA
- 9b4d363 Update TCA structure
- 45e46ad Reformat TCA configuration
- c25bfef [UPDATE] Update icon registration
- d8b54c0 Update ext_emconf.php
- 255d268 Apply phpcs
- 5a14aaa Add rector, phpstan and grumphp
- d39ded3 Add missing TYPO3 dependencies
- e5aa788 Unset viewhelper arguments as required
- 96aaf68 Fix license comment blocK
- 9814c2a Fix accessing wrong settings uidOfUserGroup value
- 4a12dc1 Set viewhelper arguments as required
- 92d2f4a Add images to topics/posts which should be activated by admin
- 53d467c [NEW] Add access check for topic/post controller "new" action
- 09ceb90 Add todo to IsCreateButtonAllowedViewHelper
- d915c67 Fix typo in license docblock
- 4918cf4 [BUGIFX] Fixes #14: Add missing check of the result from "getUsersPropertyName"
- c6fb56c Add interfaces for topic and post domain model
- 4ff9ea4 Call persistAll before dispatch event so a valid UID is present
- 096f37f Prevent calling "mailToTopicCreator", should be an event that allows third-party extensions to customize the mail content.
- 0dbfbfd Pass settings to create event
- 45fe8d2 Pass settings to topic create event
- 71b0ca6 Remove strip_tags for description to use RTE
- b27b4a7 Fix wording
- 4f6739b Change getImages() return type
- d355f2b [NEW] Add image mime type configuration
- 473330b Drop TYPO3 v10 support
- 90e7eab [NEW] Add event dispatched right after creating a new post
- 45eed0a [NEW] Add event dispatched right after creating a new topic
- 34fe437 [UPDATE] Update composer.json
- c84b6c8 Replace extkey for deprecated TER repo
- a3c3cd7 Use TEXT for description

## Contributors

- Axel Seemann
- Gitsko
- Gordana Kojic
- Rico Sonntag
- Stefan Frömken
- Stefan Frömken

# 4.0.3

## MISC

- 36cd79e Use correct ordering in documentation
- d45858b Update version to 4.0.3
- 0e98e36 Update Route section of documentation
- 969ec45 Correct indents in Includes.rst.txt

## Contributors

- Stefan Froemken

# 4.0.2

## MISC

- 093857d Update version to 4.0.2
- 074ddfd Allow TYPO3 versions higher than 11.5.16
- 5f80ddf Add a default value for hideAtCreation
- 5a16d1d Replace empty with string comparison in ExtConf

## Contributors

- Stefan Froemken

# 4.0.1

## BUGFIX

- b7acca0 [BUGFIX] Use correct event in UploadMultipleFilesConverter

## MISC

- 1cf1b14 Update version to 4.0.1

## Contributors

- Stefan Froemken

# 4.0.0

## MISC

- cdbd400 Add .github to git attributes
- cb601a5 Add prophecy trait to tests
- f4befc3 Use same types for setUp/tearDown than in parent class
- 5f45f0f Remove lines mentioned by php-cs-fixer
- 72693eb Add tests for TYPO3 11
- 866a034 Remove empty spaces between tag arguments
- ed99fd6 Do not use typed properties for TYPO3 10 compatibility
- df06451 Add card-content CSS class for TYPO3 10 compatibility
- 503bfcf Update version to 4.0.0
- a3c61c7 Implement EventListeners for image upload
- dc5223d Add array key check in validators
- 973f749 Add array key check of useImages
- fd41cc6 Use correct repo in Post controller
- 88736ea Correct columns in edit view of post, without images
- 3f3e778 Add EventListener to set username as mandatory
- 173d53a Add ViewHelper to simplify authentication check for create button
- 43b642a Remove TS settings for page-browser
- 7d7d435 Use cards in admin module
- 799501b Place images at front of posts/topics, if available
- 98d1b8d Repair image upload for posts
- bcaa74a Use cards for preview instead of list-group
- 5b2cc4e Use FluidEmail for TopiController
- 38689fe Add dynamic validator for email, if needed
- c0c9a5e Add padding to preview
- 9015a9b Add ext name to f:translate in FluidMail templates
- 8919c6f Convert pforum mails to FluidMail templates
- 2ecc780 Add email template path to FluidMail
- 838757b Add translatable subject for email
- 96cc6f0 Dynamically add email validator
- d9ef06d Format preview of new post
- 7d44c69 Update form fields for new posts in fe
- aaf2d73 Replace f:widget with Pagination API
- 1633fe6 Style topic list
- 87dd763 Add pluginname and ext name to add redirect calls
- 71a118b Add bootstrap to error messages
- 9fac864 Move Auth/Content templates into forum/topic templates
- c4d754d Add SimplePagination for topics
- 169cea3 Add event to all ForumController actions
- 338d9c1 Move methods of abstract classes into topic/post controller
- 9c3790c Use bootstrap classes in forum list
- 0226b2c Use th for table header in BE module
- 57ce062 Sort posts/topics in be module by title/description
- edbc0f5 Repair redirect after activating posts/topics in be module
- 08b2584 Use btn-class for activate button in BE module
- 8686275 Restructure Administration module
- c946260 Remove old table mapping for TYPO3 9
- 1923c6e Add html-tag. Use inline f:translate style
- 502575c Use tsconfig extension for pagetsconfig file
- 8761514 Remove vendor. Use FQCN for plugin registration
- 3042d1f Add empty line at end of file
- d25a9c2 Update lines mentioned by PhpStorm Inspector
- 0cd9986 Move group check into FrontendGroupHelper
- 266f4df Add :void to setter methods in model
- dd9d9fa Remove count on array
- e817f26 Update php-cs-fixer configuration
- 6399bbc Add renderType for select columns
- bba272c Remove TYPO3 9 compatibility. Add TYPO3 11 compatibility

## Contributors

- Stefan Froemken

# 3.0.0

## TASK

- 3eaa527 Revert "[TASK] Remove empty FrontendUser and use Extbase FrontendUser instead"
- fdad114 [TASK] Update ExtConfTest
- 31275f8 [TASK] Update composer.json
- a32ab3f [TASK] Update CI
- 25ce6f2 [TASK] Remove empty FrontendUser and use Extbase FrontendUser instead
- 93b1cb3 [TASK] Replace old icons

## MISC

- 1531c58 Update Crowdin configuration file
- 590ba67 Cast request argument to int, if topic or post
- c17df93 Throw Exception, if no email was configured in extension setting
- 785dd32 Update translation for ExtConf
- 1db6e87 Update lines mentioned by php-cs-fixer
- 066d1e5 Add UnitTests
- b0ccd47 Remove @var where possible
- 974bf46 Update Crowdin configuration file
- d9bc2fb Remove unused imports
- f006f81 Remove DataTables from BE module. Update BE module
- 2d859a4 Repair BE module
- c3498c4 Update lines mentioned by php cs fixer
- b3754e6 Restructure code a little bit
- 3f980b4 Allow null for various properties
- 576c937 Better default TS configuration
- 316b39b Add translation for plugin preview
- ff94e24 Better structure of TCA
- 5041cfe add Plugin Preview
- ad601b4 Add to newContentElementBrowser
- 0ef2ce5 Remove meta language from FlexForm
- 76f8704 Add table mapping for TYPO3 10
- 675509c Rename ts files to typoscript
- 443de1b Move Extension icon to Resources/Public/Icons
- 7c2745d Remove TYPO3 columns from sql file
- d1612c2 Remove default CSH Icons
- 0121a0a Add security header
- bcca112 Move TCA related modification into TCA/Overrides
- d24f2fb Repair UnitTests
- c0645e7 Remove unused imports
- 9ccee87 Update UnitTests
- 45864ce Update lines mentioned by php cs fixer
- 87be51d Update lines mentioned by php cs fixer
- d3bd483 Replace ->assert with self::
- 23abf00 Add testing files. Add LICENSE
- 6880195 Remove TYPO3 8 and add TYPO3 9/10 compatibility
- 83fba4d Add strict types where possible
- 64cf21e Add Services.yaml for TYPO3 10 DI
- 4c42f99 Update PHP DocHeaders
- 6dd5c37 Remove default Documentation

## Contributors

- Pascal Rinker
- Stefan Froemken

# 2.0.1

## MISC

- 9461b59 Updated version to 2.0.1
- 3d142b2 Removed usage of files from fileadmin
- d33e58b Removed renderMode and className from f:flashMessage
- 99e34cb Replaced traditional array syntax with short syntax Added missing icon for anonymoususer
- 67884b4 Update TYPO3 TCA fields to new version
- b26abae Updated composer.json

## Contributors

- Pascal Rinker
- Stefan Froemken

# 2.0.0

## MISC

- 703a8ba Updated composer.json
- 16d2324 Added composer.json
- 759990c Removed  from TCA overrides, removed inject annotations
- de0b0e7 Fixed deleteAction of PostController
- ef78d01 Fixed typo
- be0e369 Updated module icon
- c535f4f Removed unnecessary fully qualified class names
- 004e833 Changed version number to 2.0.0, fixed image upload for TYPO3 8
- 6b3dc15 Removed all deprecated flashMessage attributes from Fluid-Templates, disabled cHash requirement
- dd59704 Code improvements
- 1aef1c2 moved JavaScript from Fluid template into js module, removed deprecated calls in ForumController and TCA files, updated TypoScript constants/setup
- ec3e9ba Moved TCA from ext_tables to Configuration/TCA, moved some other stuff from ext_tables to Configuration/TCA/Overrides, updated TYPO3 comment
- 6ff1032 Added extension files from pforum 1.0.0
- abf8a7c Initial commit

## Contributors

- Pascal Rinker
- Pascal Rinker

