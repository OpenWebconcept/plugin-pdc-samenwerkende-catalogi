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

    /**
     * @return string
     */
    public function getUplName(): string
    {
        $uplName = get_post_meta($this->getID(), '_owc_pdc_upl_naam', true);

        if (empty($uplName)) {
            $uplName = $this->getTitle();
        }

        return $this->stripUpl($uplName);
    }

    /**
     * @return string
     */
    public function getUplResource(): string
    {
        return 'http://standaarden.overheid.nl/owms/terms/' . $this->getUplName();
    }

    /**
     * Strip upl to required format.
     *
     * @param string $string
     * @return string
     */
    protected function stripUpl(string $string): string
    {
        // replace all spaces with dashes.
        $string = str_replace(' ', '-', strtolower($string));

        // replace all the characters except lowercase letters and dashes.
        return preg_replace("/[^a-z|-]/", "", $string);
    }
}
