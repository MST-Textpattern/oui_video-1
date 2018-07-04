<?php

/*
 * This file is part of oui_player_abcnews,
 * a oui_player v2+ extension to easily create
 * HTML5 customizable video and audio players in Textpattern CMS.
 *
 * https://github.com/NicolasGraph/oui_player_html
 *
 * Copyright (C) 2018 Nicolas Morand
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA..
 */

/**
 * Video
 *
 * Manages HTML5 <video> player.
 *
 * @package Oui\Player
 */

namespace Oui {

    if (class_exists('Oui\Audio')) {

        class Video extends Audio
        {
            protected static $patterns = array(
                'filename' => array(
                    'scheme' => '#^((?!(http|https)://(www\.)?)\S+\.(mp4|ogv|webm))$#i',
                    'id'     => '1',
                ),
                'url' => array(
                    'scheme' => '#^(((http|https)://(www\.)?)\S+\.(mp4|ogv|webm))$#i',
                    'id'     => '1',
                ),
            );
            protected static $mimeTypes = array(
                'mp4'  => 'video/mp4',
                'ogv'  => 'video/ogg',
                'webm' => 'video/webm',
            );
            protected static $dims = array(
                'width'  => '640',
                'height' => '',
                'ratio'  => '16:9',
            );
            protected static $params = array(
                'autoplay' => array(
                    'default' => '0',
                    'valid'   => array('0', '1'),
                ),
                'controls' => array(
                    'default' => '0',
                    'valid'   => array('0', '1'),
                ),
                'loop'     => array(
                    'default' => '0',
                    'valid'   => array('0', '1'),
                ),
                'muted'     => array(
                    'default' => '0',
                    'valid'   => array('0', '1'),
                ),
                'poster'  => array(
                    'default' => '',
                    'valid'   => 'url',
                ),
                'preload'  => array(
                    'default' => 'auto',
                    'valid'   => array('none', 'metadata', 'auto'),
                ),
            );

            /**
             * {@inheritdoc}
             */

            public function getPlayer($wraptag = null, $class = null)
            {
                if ($sources = $this->getSources()) {
                    $src = $sources[0];

                    unset($sources[0]);

                    $sourcesStr = array();

                    foreach ($sources as $source) {
                        $sourcesStr[] = '<source src="' . $source . '" type="' . self::getMimeType(pathinfo($source, PATHINFO_EXTENSION)). '">';
                    }

                    $params = $this->getPlayerParams();
                    $dims = $this->getSize();

                    $responsive = $this->getResponsive();
                    $wrapstyle = '';
                    $style = '';

                    extract($dims);

                    if ($responsive) {
                        $wrapstyle .= ' style="position: relative; padding-bottom:' . $height . '; height: 0; overflow: hidden"';
                        $style .= ' style="position: absolute; top: 0; left: 0; width: 100%; height: 100% ';
                        $width = $height = false;
                        $wraptag or $wraptag = 'div';
                    } else {
                        if (is_string($width)) {
                            $style ? $style .= '; width:' . $width : $style = ' style="width:' . $width . '';
                            $width = false;
                        }

                        if (is_string($height)) {
                            $style ? $style .= '; height:' . $height : $style = ' style="height:' . $height . '';
                            $height = false;
                        }
                    }

                    $style ? $style .= '"' : '';

                    $player = sprintf(
                        '<video src="%s"%s%s%s%s>%s%s</video>',
                        $src,
                        !$width ? '' : ' width="' . $width . '"',
                        !$height ? '' : ' height="' . $height . '"',
                        $style,
                        (empty($params) ? '' : ' ' . implode(self::getGlue(), $params)),
                        ($sourcesStr ? n . implode(n, $sourcesStr) : ''),
                        n . gtxt(
                            'oui_player_html_player_not_supported',
                            array(
                                '{player}' => '<video>',
                                '{src}'    => $src,
                                '{file}'   => basename($src),
                            )
                        ) . n
                    );

                    return ($wraptag) ? doTag($player, $wraptag, $class, $wrapstyle) : $player;
                }
            }
        }
    }
}
