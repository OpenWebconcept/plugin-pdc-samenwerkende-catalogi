<?php

namespace OWC\PDC\SamenwerkendeCatalogi\Models;

use OWC\PDC\Base\Models\Item;

class ScItem extends Item
{
    /**
     * @return array
     */
    public function getDoelgroepen(): array
    {
        $doelgroepTerms = $this->getTerms('pdc-doelgroep');
        $doelgroepen    = [];

        if (!is_wp_error($doelgroepTerms) && !empty($doelgroepTerms)) {
            foreach ($doelgroepTerms as $doelgroepTerm) {
                $doelgroepen = $this->assignDoelgroepen($doelgroepen, $doelgroepTerm);
            }
        }

        $doelgroepen = $this->arrayUnique($doelgroepen);

        // $doelgroepen is not allowed to be empty or else the feed will be invalid.
        if (empty($doelgroepen)) {
            return ['particulier'];
        }

        return $doelgroepen;
    }

    /**
     * @param array $doelgroepen
     * @param object $doelgroepTerm
     * 
     * @return array
     */
    private function assignDoelgroepen($doelgroepen = [], $doelgroepTerm): array
    {
        switch ($doelgroepTerm->slug) {
            case 'bewoners':
                $doelgroepen[] = 'particulier';

                break;
            case 'ondernemers':
                $doelgroepen[] = 'ondernemer';

                break;
            case 'maatschappelijkeorganisaties':
                $doelgroepen[] = 'ondernemer';

                break;
            default:
                $doelgroepen[] = 'particulier';
        }

        return $doelgroepen;
    }
}
