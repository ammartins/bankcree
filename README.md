# My Finances
========

## Installation:

<code>
  composer install
  php app/console assetic:dump
  php app/console server:run
</code>

## Exporting your Transactions and saving it as CSV

Importing data from ADN (the console command works for ABN at the moment)

<code>
123456789;EUR;20160326;20160326;1;2;1,00;A Transaction,PAS572
123456789;EUR;20160326;20160326;2;3;2,00;One more Transaction,PAS572
123456789;EUR;20160326;20160326;3;2;-1,00;We payed something,PAS572
123456789;EUR;20160327;20160327;2;6;4,00;We got money,PAS572
123456789;EUR;20160327;20160327;6;11;5,00;We got more money,PAS572
123456789;EUR;20160327;20160327;11;17;6,00;We are reach,PAS572
123456789;EUR;20160328;20160328;17;9,00;-7,00;We payed more things,PAS572
</code>

<code>
  php app/console import:csv test.csv
</code>


A Symfony project created on February 17, 2016, 9:38 pm.
