..  include:: /Includes.rst.txt


..  _introduction:

============
Introduction
============


..  _what-it-does:

What does it do?
================

`pforum` is a very tiny forum extension for TYPO3 CMS.

Features
========

*   Create forum records (only backend)
*   Create topic records (f.e. the question)
*   Create post records (f.e. the answer)
*   Store up to 2 images foreach topic and post
*   Non authorized mode. Each creation will create a new pforum own user record
*   Authorized mode. The frontend user record (fe_user) will be assigned to topics and posts
*   Inform users by mail about new posts, if a mail is provided.
*   Frontend admin users can manage topic and post records
*   Backend module to manage topics and posts

What it does not
================

Please keep in mind that we don't want to provide a full featured forum extension. In that case please use
the forum extension of Mittwald.

*   No images like smileys in textarea
*   No HTML in general in textarea
*   No avatars for users
*   No highlighting of topics and posts
*   No fixed topics or posts at top of list
*   No overview of all topics/posts of a user
*   No quoting of previous topics/posts
*   No anker links to jump to a specific topic/post
*   No birthday reminders
*   No links in general
