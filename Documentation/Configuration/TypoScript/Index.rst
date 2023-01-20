..  include:: /Includes.rst.txt


..  _typoscript:

==========
TypoScript
==========

`pforum` needs some basic TypoScript configuration. To do so you have to add an +ext template to either the root
page of your website or to a specific page which contains the `pforum` plugin.

..  rst-class:: bignums

1.  Locate page

    You have to decide where you want to insert the TypoScript template. Eithe root page or page with `pforum` plugin
    is OK.

2.  Create TypoScript template

    Switch to template module and choose the specific page from above in the pagetree. Choose
    `Click here to create an extension template` from the right frame. In the TYPO3 community it is also known as
    "+ext template".

3.  Add static template

    Choose `Info/Modify` from the upper selectbox and then click on `Edit the whole template record` button below
    the little table. On tab `Includes` locate the section `Include static (from extension)`. Use the search below
    `Available items` to search for `pforum`. Hopefully just one record is visible below. Choose it, to move that
    record to the left.

4.  Save

    If you want you can give that template a name on tab "General", save and close it.

5.  Constants Editor

    Choose `Constant Editor` from the upper selectbox.

6.  `pforum` constants

    Choose `PLUGIN.TX_PFORUM` from the category selectbox to show just `pforum` related constants

6.  Configure constants

    Adapt the constants to your needs.

7.  Configure TypoScript

    As constants will only allow modifiying a fixed selection of TypoScript you also switch to `Info/Modify` again
    and click on `Setup`. Here you have the possibility to configure all `pforum` related configuration.

View
====

view.templateRootPaths
----------------------

Default: Value from Constants *EXT:pforum/Resources/Private/Templates/*

You can override our Templates with your own SitePackage extension. We prefer to change this value in TS Constants.

view.partialRootPaths
---------------------

Default: Value from Constants *EXT:pforum/Resources/Private/Partials/*

You can override our Partials with your own SitePackage extension. We prefer to change this value in TS Constants.

view.layoutsRootPaths
---------------------

Default: Value from Constants *EXT:pforum/Resources/Layouts/Templates/*

You can override our Layouts with your own SitePackage extension. We prefer to change this value in TS Constants.


Persistence
===========

persistence.storagePid
----------------------

Set this value to a Storage Folder (PID) where you have stored the records.

Example: `plugin.tx_pforum.settings.storagePid = 21,45,3234`


Settings
========

settings.auth
-------------

Default: 1 (no authentication)

Example: `plugin.tx_pforum.settings.auth = 2`

Define, if creation of new topics and posts needs an authenticated frontend user or not.

*   Value: `1`: No authentication. Everyone can create topics and posts. We prefer using it in intranet environments.
*   Value: `2`: An authenticated frontend user is needed to create topics and posts.

..  note::

    If you choose `1` with each created topic and/or post a new pforum own user record will be created.

settings.emailIsMandatory
-------------------------

Default: 0

Example: `plugin.tx_pforum.settings.emailIsMandatory = 1`

If activated a further input field will be displayed where the user has to insert a valid email address.
Useful in case of `auth = 1`. The email address will be added to pforum own user record.

settings.usernameIsMandatory
----------------------------

Default: 0

Example: `plugin.tx_pforum.settings.usernameIsMandatory = 1`

If activated a further input field will be displayed where the user has to insert a username.
Useful in case of `auth = 1`. The username will be added to pforum own user record.

settings.useImages
------------------

Default: 0

Example: `plugin.tx_pforum.settings.useImages = 1`

If activated two additional upload fields will be added to the form of new topics and posts.

settings.uidOfAdminGroup
------------------------

Default: 0

Example: `plugin.tx_pforum.settings.uidOfAdminGroup = 14`

By default you, as an administrator, have to modify or delete topics and post record in TYPO3 backend.
With this setting you can define a frontend usergroup which should act as an administrator to edit
and delete records in frontend view.

settings.uidOfUserGroup
-----------------------

Default: 0

Example: `plugin.tx_pforum.settings.uidOfUserGroup = 26`

If authentication is required `auth = 2` you have to define a frontend usergroup which is allowed to create
new topics and posts.

settings.pidOfDetailPage
------------------------

Default: 0

Example: `plugin.tx_pforum.settings.pidOfDetailPage = 26`

By default all detail view are displayed on the same page of the forum record list. For design reasons
it may make sense to define a special detail view page.

settings.topic.hideAtCreation
-----------------------------

Default: 0

Example: `plugin.tx_pforum.settings.topic.hideAtCreation = 1`

By default every new topic created over frontend is directly visible. If you want to prevent
that you can activate that option and an administrator has to review that topic first.

settings.topic.activateByAdmin
------------------------------

Default: 0

Example: `plugin.tx_pforum.settings.topic.activateByAdmin = 1`

By default hidden records can only be activated by a backend editor. If you want your frontend
administrator to enable hidden topics you should activate this option here.

settings.post.hideAtCreation
----------------------------

Default: 0

Example: `plugin.tx_pforum.settings.post.hideAtCreation = 1`

By default every new post created over frontend is directly visible. If you want to prevent
that you can activate that option and an administrator has to review that post first.

settings.post.activateByAdmin
------------------------------

Default: 0

Example: `plugin.tx_pforum.settings.post.activateByAdmin = 1`

By default hidden records can only be activated by a backend editor. If you want your frontend
administrator to enable hidden post you should activate this option here.

settings.new.uploadFolder
-------------------------

Default: 1:user_upload/tx_pforum/

Example: `plugin.tx_pforum.settings.new.uploadFolder = 2:dropbox/pforum/`

Only valid, if you have activated `useImages`. Define the default storage location
for uploaded images in frontend context.

settings.image.*
----------------

Default:

..  code-block:: typoscript

    settings.image {
      width = 120c
      height = 90c
      minWidth = 120
      maxWidth = 120
      minHeight = 90
      maxHeight = 90
    }

With these values you can manipulate the topic and post image size.

settings.pageBrowser.itemsPerPage
---------------------------------

Default: 15

Example: `plugin.tx_pforum.settings.pageBrowser.itemsPerPage = 10`

If there are a lot of records the pagebrowser will help to navigate through all these records.
Define the max amount of records to be displayed on a page.
