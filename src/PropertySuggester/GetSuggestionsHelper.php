<?php

namespace PropertySuggester;

use PropertySuggester\Suggesters\SimplePHPSuggester;
use PropertySuggester\Suggesters\SuggesterEngine;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\EntityLookup;
use Wikibase\StoreFactory;


/**
 * API module helper to get property suggestions.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 */
class GetSuggestionsHelper {

	/**
	 * @var EntityLookup
	 */
	private $lookup;

	/**
	 * @var SuggesterEngine
	 */
	private $suggester;

	public function __construct(EntityLookup $lookup, SuggesterEngine $suggester) {
		$this->lookup = $lookup;
		$this->suggester = $suggester;
	}


	/**
	 * Provide either an entity id or a comma separated list of property ids
	 *
	 * @param string $item
	 * @param string $propertyList
	 * @return array
	 */
	public function generateSuggestions( $item, $propertyList ) {
		if ( $item !== null ) {
			$id = new  ItemId( $item );
			$item = $this->lookup->getEntity( $id );
			$suggestions = $this->suggester->suggestByItem( $item );
			return $suggestions;
		} else {
			$splitList = explode( ',', $propertyList );
			$properties = array();
			foreach ( $splitList as $id ) {
				$properties[] = PropertyId::newFromNumber( $this->cleanPropertyId( $id ) );
			}
			$suggestions = $this->suggester->suggestByPropertyIds( $properties );
			return $suggestions;
		}
	}

	/**
	 * accepts strings of the format "P123" or "123" and returns
	 * the id as int. returns 0 if the string is not of the specified format
	 *
	 * @param string $propertyId
	 * @return int
	 */
	protected function cleanPropertyId( $propertyId ) {
		if ( $propertyId[0] === 'P' ) {
			return (int)substr( $propertyId, 1 );
		}
		return (int)$propertyId;
	}

	/**
	 * Filter for entries whose label or alias starts with $search
	 * An entry needs to have a field 'label' and an array 'aliases'
	 *
	 * @param array $entries
	 * @param string $search
	 * @return array
	 */
	public function filterByPrefix( array &$entries, $search ) {
		$matchingEntries = array();
		foreach ( $entries as $entry ) {
			if ( $this->isMatch( $entry, $search ) ) {
				$matchingEntries[] = $entry;
			}
		}
		return $matchingEntries;
	}

	/**
	 * Check if entry['label'] or entry['aliases'] starts with $search
	 *
	 * @param array $entry
	 * @param string $search
	 * @return bool
	 */
	protected function isMatch( array $entry, $search ) {
		if ( stripos( $entry['label'], $search ) === 0 ) {
			return true;
		}
		if ( $entry['aliases'] ) {
			foreach ( $entry['aliases'] as $alias ) {
				if ( stripos( $alias, $search ) === 0 ) {
					return true;
				}
			}
		}
		return false;
	}
}
