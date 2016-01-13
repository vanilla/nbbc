# The New BBCode Parser (NBBC)

[![Build Status](https://img.shields.io/travis/vanilla/nbbc.svg?style=flat-square)](https://travis-ci.org/vanilla/nbbc)
[![Packagist Version](https://img.shields.io/packagist/v/vanilla/nbbc.svg?style=flat-square)](https://packagist.org/packages/vanilla/nbbc)

NBBC is a high-speed, extensible, easy-to-use validating BBCode parser. It was originally developed by Sean Werkema and
most of the core code is still his. The core NBBC was last [updated officially](http://nbbc.sourceforge.net/) in 2010.
This is forked from the best third-party fork we could find.

## Changes between this version of NBBC and the original project

This project breaks backwards-compatibility with the 1.x version of NBBC which is why it has been given a major version
number update even though no significant functionality has been added. Here are a summary of changes.

- All core classes have been moved into the Nbbc namespace to support PSR-4 autoloading and become a full composer library.
- PHP 4 is no longer supported. The minimum version of PHP required is now PHP 5.4.
- All properties on the `BBCode` class have been protected and must now be accessed with getters/setters.
- The URL auto-detection has been rewritten. It supports more general cases, but has removed support for some edge cases such as email addresses with an IP address domain.
- Images and smileys no longer check to see if files exist locally. This removes auto-generated image sizes too.

In addition to backwards-compatibility breaking changes there have been a few other changes that should not break backwards compatibility.

- Tests have been moved into a PHPUnit test suite.
- Calls to the `EmailAddressValidator` have been replaced with PHP's `filter_var()` function.
- Calls to the `Profiler` have been removed from the BBCode class. There are plenty of profiling tools out there now that don't bloat the code.
- The "@" error silencing operator has been removed wherever possible.

## A note on copyright

As noted above, most of the NBBC was written by Sean Werkema and the copyright on that code remains his. There are files that also have a copyright assigned to Vanilla Forums Inc. That additional copyright only applies to the changes made by us. It is not our intention to claim credit for the excellent work done by the original author. We are just following the rules of copyright the best we can. This library will always be licensed under the original open source license (BSDv2).
