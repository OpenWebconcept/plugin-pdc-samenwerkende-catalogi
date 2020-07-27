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
        $doelgroepen    = ['particulier'];

        if (!is_wp_error($doelgroepTerms) && !empty($doelgroepTerms)) {
            foreach ($doelgroepTerms as $doelgroepTerm) {
                $doelgroepen = $this->assignDoelgroepen($doelgroepen, $doelgroepTerm);
            }
        }

        return $this->arrayUnique($doelgroepen);
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
