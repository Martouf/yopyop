<!--{foreach from=$personnes key=key item=aPersonne}-->
BEGIN:VCARD
VERSION:3.0
N;CHARSET=UTF-8:<!--{$aPersonne.nom}-->;<!--{$aPersonne.prenom}-->;;;
FN;CHARSET=UTF-8:<!--{$aPersonne.prenom}--> <!--{$aPersonne.nom}-->
NICKNAME;CHARSET=UTF-8:<!--{$aPersonne.surnom}-->
EMAIL;type=INTERNET;type=HOME;type=pref;CHARSET=UTF-8:<!--{$aPersonne.email}-->
TEL;type=HOME;type=pref;CHARSET=UTF-8:<!--{$aPersonne.tel}-->
item1.ADR;type=HOME;type=pref;CHARSET=UTF-8:;;<!--{$aPersonne.rue}-->;<!--{$aPersonne.lieu}-->;;<!--{$aPersonne.npa}-->;<!--{$aPersonne.pays}-->
item1.X-ABADR:ch
NOTE;CHARSET=UTF-8:<!--{$aPersonne.description}-->
BDAY;value=date:<!--{$aPersonne.date_naissance}-->
CATEGORIES;CHARSET=UTF-8:
END:VCARD
<!--{/foreach}-->