BEGIN:VCALENDAR
VERSION:2.0
X-WR-CALNAME:<!--{$evenement.nom}-->
PRODID:-//Ecodev//YopYop v1.0//EN
X-WR-TIMEZONE:Europe/Zurich
CALSCALE:GREGORIAN
METHOD:PUBLISH
BEGIN:VEVENT
<!--{if $evenement.jour_entier=="true"}-->
DTSTART;VALUE=DATE:<!--{$evenement.dateDebutVcal}-->
DTEND;VALUE=DATE:<!--{$evenement.dateFinVcal}-->
<!--{else}-->
DTSTART;TZID=Europe/Zurich:<!--{$evenement.dateTimeDebutVcal}-->
DTEND;TZID=Europe/Zurich:<!--{$evenement.dateTimeFinVcal}-->
<!--{/if}-->
SUMMARY:<!--{$evenement.nom}-->
UID:<!--{$evenement.uid}-->
CATEGORIES:<!--{$evenement.tags}-->
CREATED:<!--{$evenement.dateTimeCreationVcal}-->z
LAST-MODIFIED:<!--{$evenement.dateTimeModificationVcal}-->z
DTSTAMP:<!--{$evenement.dateTimeCreationVcal}-->z
LOCATION:<!--{$evenement.lieu}-->
END:VEVENT
END:VCALENDAR