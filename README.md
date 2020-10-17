# Commission calculation tools

>[![Build Status](https://travis-ci.org/andreyserdjuk/commission-calc-tools.svg?branch=master)](https://travis-ci.org/andreyserdjuk/commission-calc-tools)
>[![codecov](https://codecov.io/gh/andreyserdjuk/commission-calc-tools/branch/master/graph/badge.svg)](https://codecov.io/gh/andreyserdjuk/commission-calc-tools)
>[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/andreyserdjuk/commission-calc-tools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/andreyserdjuk/commission-calc-tools/?branch=master)  
>Allows to calculate commission with preliminary conversion.

#### Install
```bash
composer install
```

#### Run demo and original script
First command runs refactored code, second - raw script.
```bash
php -n bin/console co:print "$(< ./demo/input.txt)"
php demo/original.php
```
will print output similar to:  
```
1
0.43
1.61
2.21
43.88
#
1
0.42390843577787
1.6006402561024
2.2043238660449
43.875525135191  
```

#### Run unit tests
```bash
php bin/phpunit
```

### Generate unit tests coverage
```bash
php bin/phpunit --coverage-html ./coverage-html
```
