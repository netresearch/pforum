..  include:: /Includes.rst.txt


..  _installation:

============
Installation
============

Composer
========

If your TYPO3 installation works in composer mode, please execute following command:

..  code-block:: bash

    composer req jweiland/pforum
    vendor/bin/typo3 extension:setup --extension=pforum

If you work with DDEV please execute this command:

..  code-block:: bash

    ddev composer req jweiland/pforum
    ddev exec vendor/bin/typo3 extension:setup --extension=pforum

ExtensionManager
================

On non composer based TYPO3 installations you can install `pforum` still over the ExtensionManager:

..  rst-class:: bignums

1.  Login

    Login to backend of your TYPO3 installation as an administrator or system maintainer.

2.  Open ExtensionManager

    Click on `Extensions` from the left menu to open the ExtensionManager.

3.  Update Extensions

    Choose `Get Extensions` from the upper selectbox and click on the `Update now` button at the upper right.

4.  Install `pforum`

    Use the search field to find `pforum`. Choose the `pforum` line from the search result and click on the cloud
    icon to install `pforum`.

Next step
=========

:ref:`Configure pforum <configuration>`.
