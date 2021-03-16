## Open-NIS Patient Care Summary (Nurse ekardex)

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=open-nis-nurse-kardex&metric=alert_status)](https://sonarcloud.io/dashboard?id=open-nis-nurse-kardex)

### Description

A WordPress-based open source electronic patient care summary, or nurse ekardex. To conform with regulatory requirements, the plugin uses BasicPHP Class Library (https://github.com/ray-ang/basicphp) to maintain confidentiality (Advanced Encryption Standard - AES) and integrity (Keyed-Hash Message Authentication Code - HMAC) of data.

The plugin automatically creates the <em>"Nurse"</em> and <em>"Nurse Admin"</em> roles upon activation, and removes the roles upon deactivation. Provide the user with the "Nurse" or "Nurse Admin" role so he or she can access the electronic kardex.

#### Define 'KARDEX_PASS' in wp-config.php

Define constant 'KARDEX_PASS' in the WordPress configuration file (wp-config.php), and provide a pass phrase. Example can be found below.

define('KARDEX_PASS', 'SecretPassPhrase123'); // Open-NIS encryption and HMAC key

#### Permalink Settings

Make sure to set <em>Common Settings</em> under <em>Permalink Settings</em> to <strong>"Post name"</strong>.

#### Shortcodes

[open-nis-add-room]<br/>
Add Patient page

[open-nis-search-room]<br/>
Search Patient page
