<?php
/**
 * Cordillera - Standard-Inhalte (DE/ES).
 * Liefert alle Texte als Array, abhaengig von der aktuellen Sprache.
 *
 * Dies sind GENERISCHE Platzhalter-Vorgaben. Die echten Inhalte werden im
 * Backend gepflegt:
 *   - Namen des Paares, Ort, Datum, Verbinder: Customizer ("Hochzeit: Einstellungen")
 *   - Lange Abschnitte (Geschichte, Programm, Anreise, Galerie, Geschenke, FAQ):
 *     je als eigene WordPress-Seite mit passendem Slug (siehe inc/sections.php).
 * Solange nichts gepflegt ist, erscheinen die neutralen Texte unten.
 *
 * @package MyM_Hochzeit
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function mym_content( $lang = null ) {
	if ( ! $lang ) { $lang = mym_preview_lang(); }

	$de = array(
		'nav' => array(
			'story' => 'Geschichte', 'program' => 'Programm', 'travel' => 'Anreise',
			'stay' => 'Übernachtung', 'gallery' => 'Galerie', 'gifts' => 'Geschenke', 'faq' => 'FAQ',
		),
		'hero' => array(
			'eyebrow' => 'Wir heiraten', 'first' => '', 'second' => '', 'conn' => '&',
			'place' => '',
			'save' => 'Save the Date', 'until' => 'bis zum grossen Tag',
			'note' => 'Reserviert Euch schon einmal einen dieser Tage:',
		),
		'cd' => array( 'days' => 'Tage', 'hours' => 'Stunden', 'mins' => 'Minuten', 'secs' => 'Sekunden' ),
		'story' => array(
			'kicker' => 'Unsere Geschichte', 'title' => 'Wie alles begann',
			'body' => array(
				'Hier erzählt ihr eure gemeinsame Geschichte — wo ihr euch kennengelernt habt und was euch verbindet.',
				'Dieser Text ist nur ein Platzhalter. Legt eine Seite mit dem Slug „geschichte" an, um ihn zu ersetzen.',
			),
		),
		'program' => array(
			'kicker' => 'Der Tag', 'title' => 'Programm des Tages',
			'note' => 'Die Zeiten sind vorläufig — die Details folgen.',
			'items' => array(
				array( '11:00', 'Trauung', 'Im engsten Kreis' ),
				array( '14:00', 'Zeremonie', 'Unter offenem Himmel' ),
				array( '15:30', 'Apéro', 'Anstossen & Begegnen' ),
				array( '18:00', 'Festessen', 'Gemeinsam an der Tafel' ),
				array( '21:00', 'Tanz & Fest', 'Bis tief in die Nacht' ),
			),
		),
		'travel' => array(
			'kicker' => 'Ort & Weg', 'title' => 'Ort & Anreise',
			'body' => 'Hier beschreibt ihr, wo gefeiert wird und wie eure Gäste am besten anreisen.',
			'maplabel' => 'Veranstaltungsort', 'mapnote' => 'Karte folgt',
			'legs' => array(
				array( 'Anreise', 'Hinweise zur Anfahrt mit dem Auto.' ),
				array( 'Öffentlich', 'Hinweise zu Zug und Bus.' ),
				array( 'Vor Ort', 'Parken, letzte Meter, Treffpunkt.' ),
			),
		),
		'stay' => array(
			'kicker' => 'Schlafen', 'title' => 'Übernachtung',
			'hotels_title' => 'Hotels & Unterkünfte', 'hotel_note' => 'Konkrete Empfehlungen mit Links folgen.',
			'hotels' => array(
				array( 'Im Zentrum', 'Zentral, ideal ohne Auto', '€€', '' ),
				array( 'Landgasthof', 'Ruhig und naturnah, im Umland', '€€', '' ),
				array( 'Hostel / Budget', 'Für preisbewusste Gäste', '€', '' ),
			),
			'board_title' => 'Unterkunfts-Börse',
			'board_sub' => 'Gäste helfen Gästen: Wer privat ein Bett anbietet — oder eines sucht — trägt sich hier ein.',
			'offer' => 'Wir bieten Unterkunft', 'seek' => 'Wir suchen Unterkunft',
			'f_name' => 'Name', 'f_type' => 'Art', 'f_places' => 'Plätze',
			'f_location' => 'Ort', 'f_date_from' => 'Von', 'f_date_to' => 'Bis',
			'f_langs' => 'Gesprochene Sprachen',
			'f_note' => 'Kurze Vorstellung / Notiz',
			'f_contact' => 'Kontakt (E-Mail oder Tel.)',
			'f_contact_note' => '— wird nicht veröffentlicht, nur für uns',
			'add' => 'Eintragen',
			'empty_offer' => 'Noch keine Angebote — sei die/der Erste!', 'empty_seek' => 'Noch keine Gesuche.',
		),
		'gallery' => array(
			'kicker' => 'Erinnerungen', 'title' => 'Foto-Galerie',
			'body' => 'Hier sammeln wir die Fotos vom grossen Tag. Schaut sie euch an — und ladet eure eigenen hoch.',
			'cta' => 'Zur Galerie & Upload',
			'note' => 'Den Galerie-Link tragt ihr im Customizer ein.',
		),
		'gifts' => array(
			'kicker' => 'Schenken', 'title' => 'Geschenke',
			'body' => 'Eure Anwesenheit ist das schönste Geschenk. Wer uns darüber hinaus etwas schenken möchte, findet hier vielleicht eine Idee:',
			'note' => 'Die Details folgen näher am Tag.',
			'items' => array(
				array( '01', 'Reisekasse', 'Ein Beitrag an unsere gemeinsame Reise.' ),
				array( '02', 'Für unser Zuhause', 'Kleine Wünsche für den gemeinsamen Alltag.' ),
				array( '03', 'Herzenssache', 'Eine Spende für ein Projekt, das uns am Herzen liegt.' ),
			),
		),
		'faq' => array(
			'kicker' => 'Gut zu wissen', 'title' => 'Häufige Fragen',
			'disclaimer' => 'Angaben ohne Gewähr — bitte vor der Reise offizielle Quellen prüfen.',
			'items' => array(
				array( 'Bis wann sollen wir zu-/absagen?', 'Hier tragt ihr eure Antwort ein.' ),
				array( 'Gibt es einen Dresscode?', 'Hier tragt ihr eure Antwort ein.' ),
				array( 'Können wir Kinder mitbringen?', 'Hier tragt ihr eure Antwort ein.' ),
				array( 'Wo können wir übernachten?', 'Hier tragt ihr eure Antwort ein.' ),
			),
		),
		'footer' => array(
			'tag' => 'Mit Liebe — con cariño', 'made' => '',
			'langnote' => 'Diese Seite gibt es auf Deutsch und auf Español.',
		),
	);

	$es = array(
		'nav' => array(
			'story' => 'Historia', 'program' => 'Programa', 'travel' => 'Cómo llegar',
			'stay' => 'Alojamiento', 'gallery' => 'Galería', 'gifts' => 'Regalos', 'faq' => 'Preguntas',
		),
		'hero' => array(
			'eyebrow' => 'Nos casamos', 'first' => '', 'second' => '', 'conn' => 'y',
			'place' => '',
			'save' => 'Reserva la fecha', 'until' => 'para el gran día',
			'note' => 'Reserva desde ya uno de estos días:',
		),
		'cd' => array( 'days' => 'Días', 'hours' => 'Horas', 'mins' => 'Minutos', 'secs' => 'Segundos' ),
		'story' => array(
			'kicker' => 'Nuestra historia', 'title' => 'Cómo empezó todo',
			'body' => array(
				'Aquí cuentan su historia juntos — dónde se conocieron y qué los une.',
				'Este texto es solo un marcador. Crea una página con el slug «historia» para reemplazarlo.',
			),
		),
		'program' => array(
			'kicker' => 'El día', 'title' => 'Programa del día',
			'note' => 'Los horarios son provisionales — más detalles pronto.',
			'items' => array(
				array( '11:00', 'Boda', 'En la intimidad' ),
				array( '14:00', 'Ceremonia', 'Bajo el cielo abierto' ),
				array( '15:30', 'Aperitivo', 'Brindis y reencuentros' ),
				array( '18:00', 'Cena de fiesta', 'Todos a la mesa' ),
				array( '21:00', 'Baile y fiesta', 'Hasta entrada la noche' ),
			),
		),
		'travel' => array(
			'kicker' => 'Lugar y ruta', 'title' => 'Lugar y cómo llegar',
			'body' => 'Aquí describen dónde se celebra y cómo llegan mejor sus invitados.',
			'maplabel' => 'Lugar del evento', 'mapnote' => 'Mapa pronto',
			'legs' => array(
				array( 'En coche', 'Indicaciones para llegar en coche.' ),
				array( 'Transporte público', 'Indicaciones de tren y bus.' ),
				array( 'En el lugar', 'Aparcamiento, últimos metros, punto de encuentro.' ),
			),
		),
		'stay' => array(
			'kicker' => 'Dormir', 'title' => 'Alojamiento',
			'hotels_title' => 'Hoteles y alojamientos', 'hotel_note' => 'Pronto añadiremos recomendaciones concretas con enlaces.',
			'hotels' => array(
				array( 'En el centro', 'Céntrico, ideal sin coche', '€€', '' ),
				array( 'Posada rural', 'Tranquila y natural, en los alrededores', '€€', '' ),
				array( 'Hostal / económico', 'Para quienes cuidan el presupuesto', '€', '' ),
			),
			'board_title' => 'Bolsa de alojamiento',
			'board_sub' => 'Invitados que ayudan a invitados: si ofreces una cama o buscas una, apúntate aquí.',
			'offer' => 'Ofrecemos alojamiento', 'seek' => 'Buscamos alojamiento',
			'f_name' => 'Nombre', 'f_type' => 'Tipo', 'f_places' => 'Plazas',
			'f_location' => 'Lugar', 'f_date_from' => 'Desde', 'f_date_to' => 'Hasta',
			'f_langs' => 'Idiomas hablados',
			'f_note' => 'Breve presentación / nota',
			'f_contact' => 'Contacto (correo o tel.)',
			'f_contact_note' => '— no se publica, solo para nosotros',
			'add' => 'Añadir',
			'empty_offer' => 'Aún no hay ofertas — ¡sé el primero!', 'empty_seek' => 'Aún no hay solicitudes.',
		),
		'gallery' => array(
			'kicker' => 'Recuerdos', 'title' => 'Galería de fotos',
			'body' => 'Aquí reunimos las fotos del gran día. Véanlas — y suban las suyas.',
			'cta' => 'Ir a la galería y subir',
			'note' => 'El enlace de la galería se configura en el Customizer.',
		),
		'gifts' => array(
			'kicker' => 'Regalar', 'title' => 'Regalos',
			'body' => 'Su presencia es el mejor regalo. Quien además quiera obsequiarnos algo, quizá encuentre aquí una idea:',
			'note' => 'Los detalles llegarán más cerca de la fecha.',
			'items' => array(
				array( '01', 'Fondo de viaje', 'Una contribución a nuestro viaje juntos.' ),
				array( '02', 'Para nuestro hogar', 'Pequeños deseos para el día a día.' ),
				array( '03', 'De corazón', 'Una donación para un proyecto que nos importa.' ),
			),
		),
		'faq' => array(
			'kicker' => 'Bueno saber', 'title' => 'Preguntas frecuentes',
			'disclaimer' => 'Información sin garantía — verifiquen en fuentes oficiales antes de viajar.',
			'items' => array(
				array( '¿Hasta cuándo confirmamos asistencia?', 'Aquí pongan su respuesta.' ),
				array( '¿Hay código de vestimenta?', 'Aquí pongan su respuesta.' ),
				array( '¿Podemos llevar a los niños?', 'Aquí pongan su respuesta.' ),
				array( '¿Dónde podemos alojarnos?', 'Aquí pongan su respuesta.' ),
			),
		),
		'footer' => array(
			'tag' => 'Con cariño — mit Liebe', 'made' => '',
			'langnote' => 'Esta página está disponible en español y en alemán.',
		),
	);

	$arr = ( $lang === 'es' ) ? $es : $de;

	/* ---- Backend-Werte ueberlagern (Customizer) ---- */
	$couple = mym_couple();
	$arr['hero']['first']  = $couple['a'];
	$arr['hero']['second'] = $couple['b'];

	$conn = ( $lang === 'es' )
		? trim( (string) get_theme_mod( 'mym_connector_es', '' ) )
		: trim( (string) get_theme_mod( 'mym_connector', '' ) );
	if ( $conn !== '' ) { $arr['hero']['conn'] = $conn; }

	$place = ( $lang === 'es' ) ? trim( (string) get_theme_mod( 'mym_place_es', '' ) ) : '';
	if ( $place === '' ) { $place = trim( (string) get_theme_mod( 'mym_place', '' ) ); }
	if ( $place !== '' ) { $arr['hero']['place'] = $place; }

	/* Footer-Signatur: "A & B · Jahr" aus Backend-Werten */
	$year = '';
	$wd   = get_theme_mod( 'mym_wedding_date', '' );
	if ( $wd ) { $ts = strtotime( $wd ); if ( $ts ) { $year = date_i18n( 'Y', $ts ); } }
	$made = trim( $couple['a'] . ' ' . $arr['hero']['conn'] . ' ' . $couple['b'] );
	$made = trim( $made, " {$arr['hero']['conn']}" );
	$arr['footer']['made'] = $made ? trim( $made . ( $year ? ' · ' . $year : '' ) ) : ( $year ? $year : '' );

	return $arr;
}
