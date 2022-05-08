<?php

namespace OWC\PDC\SamenwerkendeCatalogi\Settings;

class SettingsPageOptions
{
    /**
     * Settings defined on settings page
     *
     * @var array
     */
    private $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * URL to the portal website.
     */
    public function getPortalURL(): string
    {
        return $this->settings['_owc_setting_portal_url'] ?? '';
    }

    public function getPortalItemSlug(): string
    {
        return $this->settings['_owc_setting_portal_pdc_item_slug'] ?? '';
    }

    public function getTownCouncilLabel(): string
    {
        return $this->settings['_owc_setting_town_council_label'] ?? '';
    }

    public function getTownCouncilURI(): string
    {
        return $this->settings['_owc_setting_town_council_uri'] ?? '';
    }

    public function getGovernmentType(): string
    {
        return $this->settings['_owc_setting_government_type'] ?? 'Gemeente';
    }

    public static function make(): self
    {
        $defaultSettings = [
            '_owc_setting_portal_url'           => '',
            '_owc_setting_portal_pdc_item_slug' => '',
            '_owc_setting_town_council_label'   => '',
            '_owc_setting_town_council_uri'     => '',
        ];

        return new static(wp_parse_args(get_option('_owc_pdc_base_settings'), $defaultSettings));
    }
}
