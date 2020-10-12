## Open-NIS Patient Care Summary

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=open-nis-nurse-kardex&metric=alert_status)](https://sonarcloud.io/dashboard?id=open-nis-nurse-kardex)

### Description

A WordPress-based electronic patient care summary, or electronic nurse kardex. To conform with regulatory requirements, the plugin uses BasicPHP Class Library (https://github.com/ray-ang/basicphp) to maintain confidentiality (Advanced Encryption Standard - AES) and integrity (Keyed-Hash Message Authentication Code - HMAC) of data.

The plugin automatically creates a "Nurse" role upon activation, and removes the role upon deactivation. Provide the user with the "Nurse" role so he/she can access the electronic kardex.

### Define 'KARDEX_PASS' in wp-config.php

Define constant 'KARDEX_PASS' in the WordPress configuration file (wp-config.php), and provide pass phrase. Example can be found below.

define('KARDEX_PASS', 'SecretPassPhrase123'); // Open-NIS encryption and HMAC key

### Shortcodes

[open-nis-add-room]<br/>
Add Patient page

[open-nis-search-room]<br/>
Search Patient page
