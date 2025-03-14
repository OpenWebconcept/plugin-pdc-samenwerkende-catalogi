<?php declare(strict_types=1);

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
        $doelgroepen = [];

        if (! is_wp_error($doelgroepTerms) && ! empty($doelgroepTerms)) {
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
			case 'ondernemer':
            case 'ondernemers':
            case 'maatschappelijkeorganisaties':
                $doelgroepen[] = 'ondernemer';

                break;
            default:
                $doelgroepen[] = 'particulier';
        }

        return $doelgroepen;
    }

	public function getTownCouncilOnderwerpUrl(): string
	{
		$portalUrl = $this->getPortalURL();

		if ( filter_var($portalUrl, FILTER_VALIDATE_URL)) {
        	return $portalUrl;
        }

		return $this->getLink();
	}

    public function getUplName(): string
    {
        $uplName = get_post_meta($this->getID(), '_owc_pdc_upl_naam', true);

        return 0 < strlen($uplName) ? $uplName : '';
    }

    public function getStrippedUplName(): string
    {
        return $this->stripUpl($this->getUplName());
    }

    public function getUplResource(): string
    {
        $uplResource = get_post_meta($this->getID(), '_owc_pdc_upl_resource', true);

        return 0 < strlen($uplResource) ? $uplResource : '';
    }

    /**
     * Strip upl to required format.
     */
    protected function stripUpl(string $string): string
    {
        // Replace all spaces with dashes.
        $string = str_replace(' ', '-', strtolower($string));

        // Replace all the characters except lowercase letters and dashes.
        return preg_replace("/[^a-z|-]/", "", $string);
    }
}
