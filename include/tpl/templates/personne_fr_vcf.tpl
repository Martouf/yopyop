BEGIN:VCARD
VERSION:3.0
N;CHARSET=UTF-8:<!--{$personne.nom}-->;<!--{$personne.prenom}-->;;;
FN;CHARSET=UTF-8:<!--{$personne.prenom}--> <!--{$personne.nom}-->
NICKNAME;CHARSET=UTF-8:<!--{$personne.surnom}-->
EMAIL;type=INTERNET;type=HOME;type=pref;CHARSET=UTF-8:<!--{$personne.email}-->
TEL;type=HOME;type=pref;CHARSET=UTF-8:<!--{$personne.tel}-->
item1.ADR;type=HOME;type=pref;CHARSET=UTF-8:;;<!--{$personne.rue}-->;<!--{$personne.lieu}-->;;<!--{$personne.npa}-->;<!--{$personne.pays}-->
item1.X-ABADR:ch
NOTE;CHARSET=UTF-8:<!--{$personne.description}-->
BDAY;value=date:<!--{$personne.date_naissance}-->
CATEGORIES;CHARSET=UTF-8:
END:VCARD