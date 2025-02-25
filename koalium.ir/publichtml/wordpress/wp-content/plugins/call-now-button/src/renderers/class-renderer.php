<?php

namespace cnb\renderer;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

/**
 * A Renderer is a concept that allows the Call Now Button to have various render formats.
 *
 * At the moment, that would be a "modern" renderer (a local, non-Cloud version)
 * and a "cloud" renderer (which makes use of Call Now Button Premium)
 */
abstract class Renderer {
    /**
     * A renderer should register the various filters required for rendering
     *
     * @return void
     */
    abstract function register();
}
