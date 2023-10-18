# Darāmo lietu saraksti
Mini projekts izstrādāts profesionālās pilnveides izglītības programmas ietvaros, tā mērķis bija demonstrēt web lietotnes izstrādes procesu. 

Projekta repozitorijs atrodams https://github.com/Filipssh/saraksts
## Uzstādīšana
Projekts tika veidots uz XAMPP servera:
- `C:\xampp\apache` izveido `.htpasswd` failu ar vismaz vienu lietotāju,
  - htpasswd lietotāju var izveidot šeit: https://www.web2generators.com/apache-tools/htpasswd-generator
- `C:\xampp` izveido direktoriju un failu `private\connection.php`, kurā ir kods:

  `<?php $datubaze = new mysqli('localhost','root','','saraksts');`
- Izveido datubāzi `saraksts`, izmantojot izveides skriptus no [db/datubaze.sql](db/datubaze.sql)
  - Izveides skripts ir papildināts ar vienu lietotāju 'admin', ar paroli 'admin'
- paša projekta failus jāievieto `C:\xampp\htdocs`

## Salīdzinošie skati, kuros var apskatīt, kas kurā datumā tika izstrādāts.
2.okt   https://github.com/Filipssh/saraksts/compare/ae4914c...95a16b6

4.okt   https://github.com/Filipssh/saraksts/compare/95a16b6...6575d01

7.okt   https://github.com/Filipssh/saraksts/compare/6575d01...577bca4

9.okt   https://github.com/Filipssh/saraksts/compare/577bca4...cc7d1d6

11.okt  https://github.com/Filipssh/saraksts/compare/cc7d1d6...2879690

14.okt  https://github.com/Filipssh/saraksts/compare/2879690...5859a29

16.okt  https://github.com/Filipssh/saraksts/compare/5859a29...c5b9e21

18.okt  https://github.com/Filipssh/saraksts/compare/c5b9e21...main
