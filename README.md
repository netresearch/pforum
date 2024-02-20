# TYPO3 Extension `pforum`

![Build Status](https://github.com/jweiland-net/pforum/workflows/CI/badge.svg)

Pforum is a little lightweight forum for TYPO3

## 1 Features

* Create forums, topics and postings
* You can assign images to topics and postings
* It is possible to add topics/post as anonymous user, is configured

## 2 Usage

### 2.1 Installation

#### Installation using Composer

The recommended way to install the extension is using Composer.

Run the following command within your Composer based TYPO3 project:

```
composer require jweiland/pforum
```

#### Installation as extension from TYPO3 Extension Repository (TER)

Download and install `pforum` with the extension manager module.

### 2.2 Minimal setup

1) Include the static TypoScript of the extension.
2) Create forum records on a sysfolder.
3) Create a plugin on a page and select at least the sysfolder as startingpoint.
