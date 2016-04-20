# plugins-video-telewizjada

Skrypt PHP umożliwiający generowanie list m3u oraz odtwarzanie kanałów tv z serwisu telewizjada.

Plik należy wrzucić na własny serwer.

# Użycie:
http://ADRES_SERWERA/channels.php - Zwraca listę m3u

http://ADRES_SERWERA/channels.php?epg - Wykonuje przekierowanie do serwisu dostarczającego EPG

http://ADRES_SERWERA/channels.php?cid=X - Odtwarza kanał o identyfikatorze X (identyfikatory są automatycznie dołączane do adresów w pliku m3u).


# UWAGA

Pliki muszą być ciągle dostępne na serwrze, gdyż pośredniczą one w odtwarzaniu kanałów tv.
