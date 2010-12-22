BEGIN:VCALENDAR
VERSION:2.0
X-WR-CALNAME:<!--{$evenement.nom}-->
PRODID:-//Ecodev//YopYop v1.0//EN
X-WR-TIMEZONE:Europe/Zurich
CALSCALE:GREGORIAN
METHOD:PUBLISH
<!--{foreach from=$evenements key=key item=aEvenement}-->
BEGIN:VEVENT
<!--{if $aEvenement.jour_entier=="true"}-->
DTSTART;VALUE=DATE:<!--{$aEvenement.dateDebutVcal}-->
DTEND;VALUE=DATE:<!--{$aEvenement.dateFinVcal}-->
<!--{else}-->
DTSTART;TZID=Europe/Zurich:<!--{$aEvenement.dateTimeDebutVcal}-->
DTEND;TZID=Europe/Zurich:<!--{$aEvenement.dateTimeFinVcal}-->
<!--{/if}-->
<!--{if $aEvenement.periodicite=="+1 week"}-->
RRULE:FREQ=WEEKLY;INTERVAL=1
<!--{/if}-->
<!--{if $aEvenement.periodicite=="+1 month"}-->
RRULE:FREQ=MONTHLY;INTERVAL=1
<!--{/if}-->
<!--{if $aEvenement.periodicite=="+1 day"}-->
RRULE:FREQ=DAYLY;INTERVAL=1
<!--{/if}-->
<!--{if $aEvenement.periodicite=="+1 year"}-->
RRULE:FREQ=YEARLY;INTERVAL=1
<!--{/if}-->
SUMMARY:<!--{$aEvenement.nom}-->
UID:<!--{$aEvenement.uid}-->
CREATED:<!--{$aEvenement.dateTimeCreationVcal}-->z
LAST-MODIFIED:<!--{$aEvenement.dateTimeModificationVcal}-->z
DTSTAMP:<!--{$aEvenement.dateTimeCreationVcal}-->z
LOCATION:<!--{$aEvenement.lieuEvenement.nom}-->, <!--{$aEvenement.lieuEvenement.commune}-->
END:VEVENT
<!--{/foreach}-->
END:VCALENDAR