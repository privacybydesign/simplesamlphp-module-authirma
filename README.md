# simplesamlphp-module-authirma
SimplesamlPHP module for IRMA authentication

# Introduction
This module for simpleSAMLphp allows easy integration of IRMA authentication into an existing simpleSAMLphp setup.
This document explains how to install the simpleSAMLphp module and how to configure it.

We are going to assume you have a working simpleSAMLphp installation.
If you don't, refer to the 
[documentation](https://simplesamlphp.org/docs/stable/simplesamlphp-install)
of simpleSAMLphp itself.

# Installing the module and required libraries

## Install using composer

To install in simplesamlphp, use the composer-based [simplesamlphp module installer](https://github.com/simplesamlphp/composer-module-installer)

	composer require irma/simplesamlphp-module-authirma

## Manual install 

Download the [latest version](https://github.com/credentials/simplesamlphp-module-authirma/releases/latest) of the simpleSAMLphp IRMA module.

Install the module in the `modules/` directory of your simpleSAMLphp setup (or symlink it if you want to keep the directory structure clean).
The module is enabled by default: no need for a 'touch enable' inside the module directory.

Note that authirma depends on [php=jwt](https://github.com/firebase/php-jwt) for encoding and decoding JSON Web Tokens (JWT).
Install using composer:

	composer require firebase/php-jwt

# Configuration

See docs `/docs/authirma.md` for configuration instructions.
