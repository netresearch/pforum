..  include:: /Includes.rst.txt


..  _extensionSettings:

==================
Extension Settings
==================

Some general settings for `pforum` can be configured in *Admin Tools -> Settings*.


Tab: Basic
==========

FROM email address
------------------

Default: <empty>

Define the email address, which will be used to inform users about new topics and posts.

If this value is empty `pforum` will try to use the email address from
`$GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress']`. If this location is not set, too, sending a mail
will fail with an exception.

FROM email name
------------------

Default: <empty>

Define the senders name, which will be used to inform users about new topics and posts.

If this value is empty `pforum` will try to use the email address from
`$GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName']`. If this location is not set, too, sending a mail
will fail with an exception.
