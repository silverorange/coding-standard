silverorange Coding Standards
=============================
[![Build Status](https://travis-ci.org/silverorange/coding-standard.svg?branch=master)](https://travis-ci.org/silverorange/coding-standard)

Coding standards for silverorange PHP projects. These are rulesets to be used
with the [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer/wiki) tool.

Usage
-----
To use these rules for a project:

### 1. install as a require-dev dependency using composer
```sh
$ composer require --dev silverorange/coding-standard squizlabs/phpcs
```

### 2. add a post-install-cmd to register the coding standard with phpcs
```json
{
  "scripts": {
    "post-install-cmd": "./vendor/bin/phpcs --config-set installed_paths vendor/bin/silverorange/coding-standard/src"
  }
}
```

### 3. create a phpcs.xml
```xml
<?xml version="1.0"?>
<ruleset name="MyProjectName">
  <description>A custom coding standard.</description>

  <arg name="colors"/>
  <arg name="tab-width" value="4"/>
  <arg name="extensions" value="php"/>
  <arg name="encoding" value="utf-8"/>
  <arg name="warning-severity" value="0"/>

  <rule src="SilverorangeLegacy"/>
</ruleset>
```

### 4. create a composer script to lint the project
```json
{
  "scripts": {
    "lint": "./vendor/bin/phpcs"
  }
}
```

### 5. create a .travis.yml file to enforce the rules
```yml
language: php
php:
  - '5.6'
install: composer install
script:
  - ./vendor/bin/phpcs
    --ruleset=SilverorangeTransitional
    --tab-width=4
    --encoding=utf-8
    --warning-severity=0
    --extensions=php
    $(git diff --name-only HEAD~1)
```

Rulesets
--------
Three rulesets are provided:

### SilverorangeLegacy
Intended for linting the entire project for a legacy package. This omits some
rules we'd like to use for new code written for legacy packages in order to
run without error. It is not recommended to use this standard for new projects.

### SilverorangeTransitional
Intended for use as a post-commit hook or CI test. This ensures all new code
added to legacy packages follows best practices within the legacy guidelines.
This includes rules that will not pass for the entire project, but should pass
for all modified files in a new pull request.

### Silverorange
Based on PSR2. This should be used for all new silverorange PHP packages.

Sublime Setup
-------------
If you are using Sublime Text:

1. Install PHPCS on your local machine:

```
$ composer global require "squizlabs/php_codesniffer=*"
```

2. Install the coding standard on your local machine:

```
$ composer global config repositories.coding-standard vcs https://github.com/silverorange/coding-standard
$ composer global require "silverorange/coding-standard=dev-master"
```

3. Set up Sublime Linter with PHPCS as described [here](https://github.com/SublimeLinter/SublimeLinter-phpcs).

4. Create a local phpcs.xml for the standard you want to use. This can be stored anywhere on your local disk.

```
<?xml version="1.0"?>
<ruleset name="Silverorange">
  <description>Silverorange Coding Standard</description>

  <arg name="tab-width" value="4"/>
  <arg name="extensions" value="php"/>
  <arg name="encoding" value="utf-8"/>

  <rule ref="~/.composer/vendor/silverorange/coding-standard/src/Silverorange/ruleset.xml"/>
</ruleset>

```

5. In the Sublime Linter settings, update the following settings (do not remove the other settings):

```
{
    "user": {
        "linters": {
            "php": {
                "@disable": false,
                "args": [],
                "excludes": []
            },
            "phpcs": {
                "@disable": false,
                "args": [
                ],
                "standard": "~/Code/custom-rulesets/phpcs.xml"
            }
        },
        "phpcs_executable_path": "~/.composer/vendor/bin/phpcs"
    }
}

```

(Where phpcs_executable_path points to the file made in step 4.)

**Note** that this will fix you to a specific ruleset and would need to be updated when switching rulesets.
