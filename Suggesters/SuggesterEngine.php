<?php

include "Suggestion.php";

interface SuggesterEngine {
	/**
	 * Returns suggested attributes
	 *
	 * @since 0.1
	 *
	 * @return Snak
	 */
	public function suggestionsByAttributes($attributeValuePairs, $resultSize);
	public function suggestionsByEntity($entity, $resultSize);
}
