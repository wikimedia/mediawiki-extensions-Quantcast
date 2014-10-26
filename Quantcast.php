<?php
/**
 * Quantcast tracking extension -- adds Quantcast tracking JS code to all pages
 *
 * @file
 * @ingroup Extensions
 * @author Jack Phoenix <jack@shoutwiki.com> (forgive me)
 * @license https://en.wikipedia.org/wiki/Public_domain Public domain
 * @link https://www.mediawiki.org/wiki/Extension:Quantcast Documentation
 * @see https://bugzilla.shoutwiki.com/show_bug.cgi?id=108
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This is not a valid entry point to MediaWiki.' );
}

// Extension credits that will show up on Special:Version
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Quantcast Tracking',
	'version' => '0.3.0',
	'author' => 'Jack Phoenix',
	'descriptionmsg' => 'quantcast-desc',
	'url' => 'https://www.mediawiki.org/wiki/Extension:Quantcast',
);

// i18n
$wgMessagesDirs['Quantcast'] = __DIR__ . '/i18n';

// Groups that are excluded from Quantcast statistics
$wgQuantcastTrackingExcludedGroups = array( 'staff' );

// Hook it up!
$wgHooks['SkinAfterBottomScripts'][] = 'wfAddQuantcastTrackingCode';

/**
 * Add tracking JS to all pages for all users that are not members of excluded
 * groups (the group listed in $wgQuantcastTrackingExcludedGroups).
 *
 * @param Skin $skin
 * @param string $text bottomScripts text
 * @return bool
 */
function wfAddQuantcastTrackingCode( $skin, &$text ) {
	global $wgQuantcastTrackingExcludedGroups;

	$groups = $skin->getUser()->getEffectiveGroups();
	if ( !in_array( $wgQuantcastTrackingExcludedGroups, $groups ) ) {
		$message = $skin->msg( 'quantcast-tracking-number' )->inContentLanguage();
		// We have a custom tracking code, use it!
		if ( !$message->isDisabled() ) {
			$trackingCode = trim( $message->text() );
		} else { // use ShoutWiki's default code
			$trackingCode = $skin->msg( 'shoutwiki-quantcast-tracking-number' )->inContentLanguage()->text();
		}
		$safeCode = htmlspecialchars( $trackingCode, ENT_QUOTES );
		$text .= "\t\t" . '<!-- Start Quantcast tag -->
		<script type="text/javascript">/*<![CDATA[*/
		_qoptions = {
			qacct: "' . $safeCode . '"
		};
		/*]]>*/</script>
		<script type="text/javascript" src="http://edge.quantserve.com/quant.js"></script>
		<noscript>
		<img src="http://pixel.quantserve.com/pixel/' . $safeCode . '.gif" style="display: none;" border="0" height="1" width="1" alt="Quantcast" />
		</noscript>
		<!-- End Quantcast tag -->' . "\n\n";
	}

	return true;
}
