## Open-NIS Patient Care Summary

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=open-nis-patient-care-summary&metric=alert_status)](https://sonarcloud.io/dashboard?id=open-nis-patient-care-summary)

### Description

A WordPress-based electronic patient care summary, or electronic nursing kardex. To conform with regulatory requirements, the plugin uses BasicPHP Class Library (https://github.com/ray-ang/basicphp) to maintain confidentiality (Advanced Encryption Standard - AES) and integrity (Keyed-Hash Message Authentication Code - HMAC) of data.

The plugin automatically creates a "Nurse" role upon activation, and removes the role upon deactivation. Provide the user with the "Nurse" role so he/she can access the electronic kardex.

### BasicPHP Encryption Middleware

Activate BasicPHP Encryption middleware in the WordPress configuration file (wp-config.php), and provide pass phrase. Example can be found below.

Basic::encryption('SecretPassPhrase123'); // BasicPHP Encryption middleware

### Shortcodes

[open-nis-add-patient]<br/>
Add Patient page

[open-nis-search-patient]<br/>
Search Patient page
