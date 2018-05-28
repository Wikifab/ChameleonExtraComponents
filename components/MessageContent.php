<?php
/**
 *
 *
 * @file
 * @ingroup   Skins
 */

namespace Skins\Chameleon\Components;

use Linker;

/**
 * @ingroup Skins
 */
class MessageContent extends Component {

	/**
	 * Builds the HTML code for this component
	 *
	 * @return String the HTML code
	 */
	public function getHtml() {

		$messageKey = 'WikifabFooter';
		$element = $this->getDomElement();

		if ( $element !== null && $element->getAttribute( 'name' )){
			$messageKey = $element->getAttribute( 'name' );
		}

		$ret = $this->buildComponent($messageKey);


		return $ret;

	}

	function buildComponent($messageKey) {
		global $wgEnableSidebarCache, $wgSidebarCacheExpiry;

		$callback = function () use ( $messageKey ) {
			return wfMessage( $messageKey )->inContentLanguage()->parse();
		};

		if ( $wgEnableSidebarCache ) {
			$cache = ObjectCache::getMainWANInstance();
			$sidebar = $cache->getWithSetCallback(
					$cache->makeKey( $messageKey, $this->getSkin()->getLanguage()->getCode() ),
					MessageCache::singleton()->isDisabled()
					? $cache::TTL_UNCACHEABLE // bug T133069
					: $wgSidebarCacheExpiry,
					$callback,
					[ 'lockTSE' => 30 ]
					);
		} else {
			$sidebar = $callback();
		}

		return $sidebar;
	}
}

