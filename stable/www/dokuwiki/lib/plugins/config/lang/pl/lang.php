<?php
/**
 * polish language file
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author Grzegorz Żur <grzegorz.zur@gmail.com>
 * @author Mariusz Kujawski <marinespl@gmail.com>
 * @author Maciej Kurczewski <pipijajko@gmail.com>
 * @author Sławomir Boczek <slawkens@gmail.com>
 */
$lang['menu']                  = 'Ustawienia';
$lang['error']                 = 'Ustawienia nie zostały zapisane z powodu błędnych wartości, przejrzyj je i ponów próbę zapisu. <br/> Niepoprawne wartości są wyróżnione kolorem czerwonym.';
$lang['updated']               = 'Ustawienia zostały zmienione.';
$lang['nochoice']              = '(brak innych możliwości)';
$lang['locked']                = 'Plik ustawień nie mógł zostać zmieniony, upewnij się, czy uprawnienia do pliku są odpowiednie.';
$lang['danger']                = 'Uwaga: Zmiana tej opcji może uniemożliwić dostęp do twojej wiki oraz konfogiracji.';
$lang['warning']               = 'Ostrzeżenie: Zmiana tej opcji może spowodować nieporządane skutki.';
$lang['security']              = 'Alert bezpieczeństwa: Zmiana tej opcji może obniżyć bezpieczeństwo.';
$lang['_configuration_manager'] = 'Menadżer konfiguracji';
$lang['_header_dokuwiki']      = 'Ustawienia DokuWiki';
$lang['_header_plugin']        = 'Ustawienia wtyczek';
$lang['_header_template']      = 'Ustawienia motywu';
$lang['_header_undefined']     = 'Inne ustawienia';
$lang['_basic']                = 'Podstawowe';
$lang['_display']              = 'Wygląd';
$lang['_authentication']       = 'Autoryzacja';
$lang['_anti_spam']            = 'Spam';
$lang['_editing']              = 'Edycja';
$lang['_links']                = 'Odnośniki';
$lang['_media']                = 'Media';
$lang['_advanced']             = 'Zaawansowane';
$lang['_network']              = 'Sieć';
$lang['_plugin_sufix']         = 'Wtyczki';
$lang['_template_sufix']       = 'Motywy';
$lang['_msg_setting_undefined'] = 'Brak danych o ustawieniu.';
$lang['_msg_setting_no_class'] = 'Brak kategorii ustawień.';
$lang['_msg_setting_no_default'] = 'Brak wartości domyślnej.';
$lang['fmode']                 = 'Tryb tworzenia pliku';
$lang['dmode']                 = 'Tryb tworzenia katalogu';
$lang['lang']                  = 'Język';
$lang['basedir']               = 'Katalog główny';
$lang['baseurl']               = 'Główny URL';
$lang['savedir']               = 'Katalog z danymi';
$lang['start']                 = 'Tytuł strony początkowej';
$lang['title']                 = 'Tytuł wiki';
$lang['template']              = 'Motyw';
$lang['license']               = 'Pod jaką licencją publikować treści wiki?';
$lang['fullpath']              = 'Wyświetlanie pełnych ścieżek';
$lang['recent']                = 'Ilość ostatnich zmian';
$lang['breadcrumbs']           = 'Długość śladu';
$lang['youarehere']            = 'Ślad według struktury';
$lang['typography']            = 'Konwersja cudzysłowu, myślników itp.';
$lang['htmlok']                = 'Wstawki HTML';
$lang['phpok']                 = 'Wstawki PHP';
$lang['dformat']               = 'Format daty';
$lang['signature']             = 'Podpis';
$lang['toptoclevel']           = 'Minimalny poziom spisu treści';
$lang['tocminheads']           = 'Minimalna liczba nagłówków niezbędna do wytworzenia spisu treści.';
$lang['maxtoclevel']           = 'Maksymalny poziom spisu treści';
$lang['maxseclevel']           = 'Maksymalny poziom podziału na sekcje edycyjne';
$lang['camelcase']             = 'Bikapitalizacja odnośników (CamelCase)';
$lang['deaccent']              = 'Podmieniaj znaki spoza ASCII w nazwach';
$lang['useheading']            = 'Pierwszy nagłówek jako tytuł';
$lang['refcheck']              = 'Sprawdzanie odwołań przed usunięciem pliku';
$lang['refshow']               = 'Ilość pokazywanych odwołań do pliku';
$lang['allowdebug']            = 'Debugowanie (niebezpieczne!)';
$lang['usewordblock']          = 'Blokowanie spamu na podstawie słów';
$lang['indexdelay']            = 'Okres indeksowania w sekundach';
$lang['relnofollow']           = 'Nagłówek rel="nofollow" dla odnośników zewnętrznych';
$lang['mailguard']             = 'Utrudnianie odczytu adresów e-mail';
$lang['iexssprotect']          = 'Wykrywanie złośliwego kodu JavaScript i HTML w plikach';
$lang['showuseras']            = 'Sposób wyświetlania nazwy użytkownika, który ostatnio edytował stronę';
$lang['useacl']                = 'Kontrola uprawnień ACL';
$lang['autopasswd']            = 'Automatyczne generowanie haseł';
$lang['authtype']              = 'Typ autoryzacji';
$lang['passcrypt']             = 'Kodowanie hasła';
$lang['defaultgroup']          = 'Domyślna grupa';
$lang['superuser']             = 'Administrator - grupa lub użytkownik z pełnymi uprawnieniami';
$lang['manager']               = 'Menadżer - grupa lub użytkownik z uprawnieniami do zarządzania wiki';
$lang['profileconfirm']        = 'Potwierdzanie zmiany profilu hasłem';
$lang['disableactions']        = 'Wyłącz akcje DokuWiki';
$lang['disableactions_check']  = 'Sprawdzanie';
$lang['disableactions_subscription'] = 'Subskrypcje';
$lang['disableactions_nssubscription'] = 'Subskrypcje katalogów';
$lang['disableactions_wikicode'] = 'Pokazywanie źródeł';
$lang['disableactions_other']  = 'Inne akcje (oddzielone przecinkiem)';
$lang['sneaky_index']          = 'Domyślnie, Dokuwiki pokazuje wszystkie katalogi w indeksie. Włączenie tej opcji ukryje katalogi, do których użytkownik nie ma praw. Może to spowodować ukrycie podkatalogów, do których użytkownik ma prawa. Ta opcja może spowodować błędne działanie indeksu w połączeniu z pewnymi konfiguracjami praw dostępu.';
$lang['auth_security_timeout'] = 'Czas wygaśnięcia uwierzytelnienia (w sekundach)';
$lang['securecookie']          = 'Czy ciasteczka wysłane do przeglądarki przez HTTPS powinny być przez nią odsyłane też tylko przez HTTPS? Odznacz tę opcję tylko wtedy, gdy logowanie użytkowników jest zabezpieczone SSL, ale przeglądanie stron odbywa się bez zabezpieczenia.';
$lang['xmlrpc']                = 'Włącz/wyłącz interfejs XML-RPC';
$lang['xmlrpcuser']            = 'Lista użytkowników i grup, którzy mogą korzystać z protokołu XML-RPC. Nazwy grup i użytkowników rozdziel przecinkami, puste pole oznacza dostęp dla wszystkich.';
$lang['updatecheck']           = 'Sprawdzanie aktualizacji i bezpieczeństwa. DokuWiki będzie kontaktować się z serwerem splitbrain.org.';
$lang['userewrite']            = 'Proste adresy URL';
$lang['useslash']              = 'Używanie ukośnika jako separatora w adresie URL';
$lang['usedraft']              = 'Automatyczne zapisywanie szkicu podczas edycji';
$lang['sepchar']               = 'Znak rozdzielający wyrazy nazw';
$lang['canonical']             = 'Kanoniczne adresy URL';
$lang['autoplural']            = 'Automatyczne tworzenie liczby mnogiej';
$lang['compression']           = 'Metoda kompresji dla usuniętych plików';
$lang['cachetime']             = 'Maksymalny wiek cache w sekundach';
$lang['locktime']              = 'Maksymalny wiek blokad w sekundach';
$lang['fetchsize']             = 'Maksymalny rozmiar pliku (w bajtach) jaki można pobrać z zewnątrz';
$lang['notify']                = 'Wysyłanie powiadomień na adres e-mail';
$lang['registernotify']        = 'Prześlij informacje o nowych użytkownikach na adres e-mail';
$lang['mailfrom']              = 'Adres e-mail tego wiki';
$lang['gzip_output']           = 'Używaj GZIP dla XHTML';
$lang['gdlib']                 = 'Wersja biblioteki GDLib';
$lang['im_convert']            = 'Ścieżka do programu imagemagick';
$lang['jpg_quality']           = 'Jakość kompresji JPG (0-100)';
$lang['subscribers']           = 'Subskrypcja';
$lang['compress']              = 'Kompresja arkuszy CSS i plików JavaScript';
$lang['hidepages']             = 'Ukrywanie stron pasujących do wzorca (wyrażenie regularne)';
$lang['send404']               = 'Nagłówek "HTTP 404/Page Not Found" dla nieistniejących stron';
$lang['sitemap']               = 'Okres generowania Google Sitemap (w dniach)';
$lang['broken_iua']            = 'Czy funkcja "ignore_user_abort" działa? Jeśli nie, może to powodować problemy z indeksem przeszukiwania. Funkcja nie działa przy konfiguracji oprogramowania IIS+PHP/CGI. Szczegółowe informacje: <a href="http://bugs.splitbrain.org/?do=details&amp;task_id=852">Bug 852</a>.';
$lang['xsendfile']             = 'Użyj nagłówka HTTP X-Sendfile w celu przesyłania statycznych plików. Serwer HTTP musi obsługiwać ten nagłówek.';
$lang['renderer_xhtml']        = 'Mechanizm renderowania głównej treści strony (xhtml)';
$lang['renderer__core']        = '%s (dokuwiki)';
$lang['renderer__plugin']      = '%s (wtyczka)';
$lang['rememberme']            = 'Pozwól na ciasteczka automatycznie logujące (pamiętaj mnie)';
$lang['rss_type']              = 'Typ RSS';
$lang['rss_linkto']            = 'Odnośniki w RSS';
$lang['rss_content']           = 'Rodzaj informacji wyświetlanych w RSS ';
$lang['rss_update']            = 'Okres aktualizacji RSS (w sekundach)';
$lang['recent_days']           = 'Ilość ostatnich zmian (w dniach)';
$lang['rss_show_summary']      = 'Podsumowanie w tytule';
$lang['target____wiki']        = 'Okno docelowe odnośników wewnętrznych';
$lang['target____interwiki']   = 'Okno docelowe odnośników do innych wiki';
$lang['target____extern']      = 'Okno docelowe odnośników zewnętrznych';
$lang['target____media']       = 'Okno docelowe odnośników do plików';
$lang['target____windows']     = 'Okno docelowe odnośników zasobów Windows';
$lang['proxy____host']         = 'Proxy - serwer';
$lang['proxy____port']         = 'Proxy - port';
$lang['proxy____user']         = 'Proxy - nazwa użytkownika';
$lang['proxy____pass']         = 'Proxy - hasło';
$lang['proxy____ssl']          = 'Proxy - SSL';
$lang['safemodehack']          = 'Bezpieczny tryb (przez FTP)';
$lang['ftp____host']           = 'FTP - serwer';
$lang['ftp____port']           = 'FTP - port';
$lang['ftp____user']           = 'FTP - nazwa użytkownika';
$lang['ftp____pass']           = 'FTP - hasło';
$lang['ftp____root']           = 'FTP - katalog główny';
$lang['license_o_']            = 'Nie wybrano żadnej';
$lang['typography_o_0']        = 'brak';
$lang['typography_o_1']        = 'tylko podwójne cudzysłowy';
$lang['typography_o_2']        = 'wszystkie cudzysłowy (nie działa we wszystkich przypadkach)';
$lang['userewrite_o_0']        = 'brak';
$lang['userewrite_o_1']        = '.htaccess';
$lang['userewrite_o_2']        = 'dokuwiki';
$lang['deaccent_o_0']          = 'zostaw oryginalną pisownię';
$lang['deaccent_o_1']          = 'usuń litery';
$lang['deaccent_o_2']          = 'zamień na ASCII';
$lang['gdlib_o_0']             = 'biblioteka GDLib niedostępna';
$lang['gdlib_o_1']             = 'wersja 1.x';
$lang['gdlib_o_2']             = 'automatyczne wykrywanie';
$lang['rss_type_o_rss']        = 'RSS 0.91';
$lang['rss_type_o_rss1']       = 'RSS 1.0';
$lang['rss_type_o_rss2']       = 'RSS 2.0';
$lang['rss_type_o_atom']       = 'Atom 0.3';
$lang['rss_type_o_atom1']      = 'Atom 1.0';
$lang['rss_content_o_abstract'] = 'Streszczenie';
$lang['rss_content_o_diff']    = 'Różnice';
$lang['rss_content_o_htmldiff'] = 'Różnice w postaci HTML';
$lang['rss_content_o_html']    = 'Pełna strona w postaci HTML';
$lang['rss_linkto_o_diff']     = 'różnice';
$lang['rss_linkto_o_page']     = 'zmodyfikowana strona';
$lang['rss_linkto_o_rev']      = 'lista zmian';
$lang['rss_linkto_o_current']  = 'aktualna strona';
$lang['compression_o_0']       = 'brak';
$lang['compression_o_gz']      = 'gzip';
$lang['compression_o_bz2']     = 'bz2';
$lang['xsendfile_o_0']         = 'nie używaj';
$lang['xsendfile_o_1']         = 'Specyficzny nagłówek lightttpd (poniżej wersji 1.5)';
$lang['xsendfile_o_2']         = 'Standardowy nagłówek HTTP X-Sendfile';
$lang['xsendfile_o_3']         = 'Specyficzny nagłówek Nginx X-Accel-Redirect';
$lang['showuseras_o_loginname'] = 'Login użytkownika';
$lang['showuseras_o_username'] = 'Pełne nazwisko użytkownika';
$lang['showuseras_o_email']    = 'E-mail użytkownika (ukrywanie według ustawień mailguard)';
$lang['showuseras_o_email_link'] = 'Adresy e-mail użytkowników w formie linku mailto:';
$lang['useheading_o_0']        = 'Nigdy';
$lang['useheading_o_navigation'] = 'W nawigacji';
$lang['useheading_o_content']  = 'W treści';
$lang['useheading_o_1']        = 'Zawsze';
