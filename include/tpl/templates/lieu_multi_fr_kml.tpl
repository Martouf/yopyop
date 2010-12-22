<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://earth.google.com/kml/2.2">
<Document>
	<name><!--{$file_name}--></name>
	
		<!--{foreach from=$lieux key=key item=aLieu}-->
			
			<Placemark>
				<name><!--{$aLieu.nom}--></name>
				<description>
					<![CDATA[
						<!--{$aLieu.description}-->
					]]>
				</description>
				<Point>
					<coordinates><!--{$aLieu.longitude}-->,<!--{$aLieu.latitude}-->,<!--{$aLieu.altitude}--></coordinates>
				</Point>
			</Placemark>
		<!--{/foreach}-->
</Document>
</kml>