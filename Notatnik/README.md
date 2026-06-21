Notatnik osobisty

Aplikacja webowa do tworzenia, organizacji i wyszukiwania notatek tekstowych.  
Umożliwia dodawanie notatek z tytułem, treścią, tagiem, priorytetem i załącznikiem.  
Notatki można filtrować, sortować i przeszukiwać.

 Funkcje

- Tworzenie, edytowanie i usuwanie notatek
- Filtrowanie po tagach i priorytecie notatki (Wysoki, Średni, Niski)
- Pełnotekstowe wyszukiwanie w tytule i treści
- Sortowanie według daty oraz po tytule i priorytecie
- Dodawanie załączników (np. zdjęcie listy zakupów)
- Podsumowanie notatek

Wymagania systemu

- PHP w wersji 8.0 lub nowszej
- XAMPP (lub inny serwer z Apache + PHP + SQLite)
- SQLite (wbudowany w PHP)
- Przeglądarka internetowa

Instrukcja uruchomienia na XAMPP

1. Pobierz projekt
Sklonuj repozytorium lub pobierz pliki ZIP:

git clone https://github.com/APPekala/Projekt-na-programowanie.git

2. Przenieś projekt do katalogu htdocs
   C:\xampp\htdocs\

3. Uruchom Apache w XAMPP

 Otwórz XAMPP Control Panel
 Kliknij Start przy Apache

4. Przygotuj bazę danych
Aplikacja korzysta z SQLite – baza danych tworzona jest automatycznie, ale musisz utworzyć tabele.

Opcja A: Przez terminal
Otwórz CMD i wykonaj:

cmd
cd C:\xampp\htdocs\notatnik
sqlite3 database/database.sqlite < database/schema.sql

Opcja B: Przez DB Browser for SQLite
Otwórz DB Browser for SQLite
Kliknij Otwórz bazę danych → wybierz database/database.sqlite
Przejdź do zakładki Wykonaj SQL
Wklej zawartość pliku database/schema.sql
Kliknij Uruchom

5. Nadaj uprawnienia do zapisu
W systemie Windows uprawnienia są zwykle domyślnie ustawione, ale jeśli coś nie działa:
Kliknij prawym przyciskiem na folder database → Właściwości → Zabezpieczenia
Dodaj użytkownika Everyone z pełnymi uprawnieniami (lub ustaw dla IIS_IUSRS)
Powtórz dla folderu public\uploads

6. Skonfiguruj BASE_URL (jeśli trzeba)
Otwórz config/config.php – w większości przypadków domyślna wartość jest poprawna:
php
define('BASE_URL', '/notatnik/public/');

7. Uruchom aplikację
W przeglądarce wpisz:

http://localhost/notatnik/public/


Docelowa ocena 3+
