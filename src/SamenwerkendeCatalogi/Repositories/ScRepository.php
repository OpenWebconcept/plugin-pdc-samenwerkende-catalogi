<?php

namespace OWC\PDC\SamenwerkendeCatalogi\Repositories;

use OWC\PDC\Base\Repositories\AbstractRepository;
use OWC\PDC\SamenwerkendeCatalogi\Models\ScItem;
use WP_Post;

/**
 * Model for the item
 */
class ScRepository extends AbstractRepository
{
    const PREFIX = '_owc_';

    /**
     * Type of model.
     *
     * @var string $posttype
     */
    protected $posttype = 'pdc-item';

    /**
     * Container with fields, associated with this model.
     *
     * @var array $globalFields
     */
    protected static $globalFields = [];

    /**
     * Transform a single WP_Post item.
     *
     * @param WP_Post $post
     *
     * @return self
     */
    public function transform(\WP_Post $post)
    {
        return ScItem::makeFrom($post);
    }
}
