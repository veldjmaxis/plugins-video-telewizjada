# plugins-video-telewizjada

Skrypt PHP umożliwiający generowanie list m3u oraz odtwarzanie kanałów tv z serwisu telewizjada.

Plik należy wrzucić na własny serwer.

# Użycie:
    http://ADRES_SERWERA/channels.php - Zwraca listę m3u

    http://ADRES_SERWERA/channels.php?epg - Wykonuje przekierowanie do serwisu dostarczającego EPG

    http://ADRES_SERWERA/channels.php?cid=X - Odtwarza kanał o identyfikatorze X (identyfikatory są automatycznie dołączane do adresów w pliku m3u).

Klasa Telewizjada zawiera dodatkowo dwa settery, które można ustawić w pliku channels.php

    $telewizjada->setHtmlOutputFormat(true) - Formatuje dane wyjściowe na html (domyślnie: false)

    $telewizjada->setOnlineOnly(true) - Do listy dodawane są wyłącznie kanały, które nadają w danej chwili (domyślnie: false)

# UWAGA

Pliki muszą być ciągle dostępne na serwrze, gdyż pośredniczą one w odtwarzaniu kanałów tv.

# Informacja

Część kodu ze skryptu została napisana przez nieznaną mi osobę. Jeżeli jesteś twórcą tego kodu, daj mi znać, abym mógł ująć Cię jako współtwórcę.

Pozdrawiam