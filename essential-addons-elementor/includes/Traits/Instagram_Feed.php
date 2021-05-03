<?php

namespace Essential_Addons_Elementor\Pro\Traits;

// use \Essential_Addons_Elementor\Classes\Helper;

trait Instagram_Feed
{
    public function instafeed_render_items()
    {
        // check if ajax request
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'instafeed_load_more') {
            // check ajax referer
            check_ajax_referer('essential-addons-elementor', 'security');

            // init vars
            $page = $_REQUEST['page'];
            parse_str($_REQUEST['settings'], $settings);
        } else {
            // init vars
            $page = 0;
            $settings = $this->get_settings();
        }

        $key = 'eael_instafeed_'.md5(str_replace('.', '_', $settings['eael_instafeed_access_token']));
        $html = '';

        if (get_transient($key) === false) {
            $request_args = array(
                'timeout' => 10,
            );
            $instagram_data = wp_remote_retrieve_body(wp_remote_get('https://graph.instagram.com/me/media/?fields=username,id,caption,media_type,media_url,permalink,thumbnail_url,timestamp&limit=200&access_token=' . $settings['eael_instafeed_access_token'],
                $request_args));
            $data_check = json_decode($instagram_data, true);
            if (!empty($data_check['data'])) {
                set_transient($key, $instagram_data, 1800);
            }
        } else {
            $instagram_data = get_transient($key);
        }

        $instagram_data = json_decode($instagram_data, true);
        //$settings['eael_instafeed_layout'] = 'overlay';
        if (empty($instagram_data['data'])) {
            return;
        }

        if (empty($settings['eael_instafeed_image_count']['size'])) {
            return;
        }

        switch ($settings['eael_instafeed_sort_by']) {
            case 'most-recent':
                usort($instagram_data['data'], function ($a, $b) {
                    return strtotime($a['timestamp']) < strtotime($b['timestamp']);
                });
                break;

            case 'least-recent':
                usort($instagram_data['data'], function ($a, $b) {
                    return strtotime($a['timestamp']) > strtotime($b['timestamp']);
                });
                break;

//        case 'most-liked':
                //            usort( $instagram_data['data'], function ( $a, $b ) {
                //                return $a['likes']['count'] <= $b['likes']['count'];
                //            } );
                //            break;
                //
                //        case 'least-liked':
                //            usort( $instagram_data['data'], function ( $a, $b ) {
                //                return $a['likes']['count'] >= $b['likes']['count'];
                //            } );
                //            break;
                //
                //        case 'most-commented':
                //            usort( $instagram_data['data'], function ( $a, $b ) {
                //                return $a['comments']['count'] <= $b['comments']['count'];
                //            } );
                //            break;
                //
                //        case 'least-commented':
                //            usort( $instagram_data['data'], function ( $a, $b ) {
                //                return $a['comments']['count'] >= $b['comments']['count'];
                //            } );
                //            break;
        }

        if ($items = $instagram_data['data']) {
            $items = array_splice($items, ($page * $settings['eael_instafeed_image_count']['size']),
                $settings['eael_instafeed_image_count']['size']);

            foreach ($items as $item) {
                if ('yes' === $settings['eael_instafeed_link']) {
                    $target = ($settings['eael_instafeed_link_target']) ? 'target=_blank' : 'target=_self';
                } else {
                    $item['permalink'] = '#';
                    $target = '';
                }

                $image_src = ($item['media_type'] == 'VIDEO') ? $item['thumbnail_url'] : $item['media_url'];

                if ($settings['eael_instafeed_layout'] == 'overlay') {
                    $html .= '<a href="' . $item['permalink'] . '" ' . esc_attr($target) . ' class="eael-instafeed-item">
                        <div class="eael-instafeed-item-inner">
                            <img class="eael-instafeed-img" src="' . $image_src . '">

                            <div class="eael-instafeed-caption">
                                <div class="eael-instafeed-caption-inner">';
                    if ($settings['eael_instafeed_overlay_style'] == 'simple' || $settings['eael_instafeed_overlay_style'] == 'standard') {
                        $html .= '<div class="eael-instafeed-icon">
                                            <i class="fab fa-instagram" aria-hidden="true"></i>
                                        </div>';
                    } else {
                        if ($settings['eael_instafeed_overlay_style'] == 'basic') {
                            if ($settings['eael_instafeed_caption'] && !empty($item['caption'])) {
                                $html .= '<p class="eael-instafeed-caption-text">' . substr($item['caption'], 0,
                                    60) . '...</p>';
                            }
                        }
                    }

                    $html .= '<div class="eael-instafeed-meta">';
                    if ($settings['eael_instafeed_overlay_style'] == 'basic' && $settings['eael_instafeed_date']) {
                        $html .= '<span class="eael-instafeed-post-time"><i class="far fa-clock" aria-hidden="true"></i> ' . date("d M Y",
                            strtotime($item['timestamp'])) . '</span>';
                    }
                    if ($settings['eael_instafeed_overlay_style'] == 'standard') {
                        if ($settings['eael_instafeed_caption'] && !empty($item['caption'])) {
                            $html .= '<p class="eael-instafeed-caption-text">' . substr($item['caption'], 0,
                                60) . '...</p>';
                        }
                    }
//                    if ( $settings['eael_instafeed_likes'] ) {
                    //                        $html .= '<span class="eael-instafeed-post-likes"><i class="far fa-heart" aria-hidden="true"></i> ' . $item['likes']['count'] . '</span>';
                    //                    }
                    //                    if ( $settings['eael_instafeed_comments'] ) {
                    //                        $html .= '<span class="eael-instafeed-post-comments"><i class="far fa-comments" aria-hidden="true"></i> ' . $item['comments']['count'] . '</span>';
                    //                    }

                    $html .= '</div>';
                    $html .= '</div>
                            </div>
                        </div>
                    </a>';
                } else {

                    $html .= '<div class="eael-instafeed-item">
                        <div class="eael-instafeed-item-inner">
                            <header class="eael-instafeed-item-header clearfix">
                               <div class="eael-instafeed-item-user clearfix">';
                    if ($settings['eael_instafeed_show_profile_image'] == 'yes' && !empty($settings['eael_instafeed_profile_image']['url'])) {
                        $html .= '<a href="//www.instagram.com/' . $item['username'] . '"><img src="' . $settings['eael_instafeed_profile_image']['url'] . '" alt="' . $item['username'] . '" class="eael-instafeed-avatar"></a>';
                    }
                    if ($settings['eael_instafeed_show_username'] == 'yes' && !empty($settings['eael_instafeed_username'])) {
                        $html .= '<a href="//www.instagram.com/' . $item['username'] . '"><p class="eael-instafeed-username">' . $settings['eael_instafeed_username'] . '</p></a>';
                    }

                    $html .= '</div>';
                    $html .= '<span class="eael-instafeed-icon"><i class="fab fa-instagram" aria-hidden="true"></i></span>';

                    if ($settings['eael_instafeed_date'] && $settings['eael_instafeed_card_style'] == 'outer') {
                        $html .= '<span class="eael-instafeed-post-time"><i class="far fa-clock" aria-hidden="true"></i> ' . date("d M Y",
                            strtotime($item['timestamp'])) . '</span>';
                    }
                    $html .= '</header>
                            <a href="' . $item['permalink'] . '" ' . esc_attr($target) . ' class="eael-instafeed-item-content">
                                <img class="eael-instafeed-img" src="' . $image_src . '">';

                    if ($settings['eael_instafeed_card_style'] == 'inner' && $settings['eael_instafeed_caption'] && !empty($item['caption'])) {
                        $html .= '<div class="eael-instafeed-caption">
                                        <div class="eael-instafeed-caption-inner">
                                            <div class="eael-instafeed-meta">
                                                <p class="eael-instafeed-caption-text">' . substr($item['caption'], 0,
                            60) . '...</p>
                                            </div>
                                        </div>
                                    </div>';
                    }
                    $html .= '</a>
                            <footer class="eael-instafeed-item-footer">
                                <div class="clearfix">';
                    //if ( $settings['eael_instafeed_likes'] ) {
                    //                        $html .= '<span class="eael-instafeed-post-likes"><i class="far fa-heart" aria-hidden="true"></i> ' . $item['likes']['count'] . '</span>';
                    //                    }
                    //                    if ( $settings['eael_instafeed_comments'] ) {
                    //                        $html .= '<span class="eael-instafeed-post-comments"><i class="far fa-comments" aria-hidden="true"></i> ' . $item['comments']['count'] . '</span>';
                    //                    }
                    if ($settings['eael_instafeed_card_style'] == 'inner' && $settings['eael_instafeed_date']) {
                        $html .= '<span class="eael-instafeed-post-time"><i class="far fa-clock" aria-hidden="true"></i> ' . date("d M Y",
                            strtotime($item['timestamp'])) . '</span>';
                    }
                    $html .= '</div>';

                    if ($settings['eael_instafeed_card_style'] == 'outer' && $settings['eael_instafeed_caption'] && !empty($item['caption'])) {
                        $html .= '<p class="eael-instafeed-caption-text">' . substr($item['caption'], 0, 60) . '...</p>';
                    }
                    $html .= '</footer>
                        </div>
                    </div>';
                }
            }
        }

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'instafeed_load_more') {
            wp_send_json([
                'num_pages' => ceil(count($instagram_data['data']) / $settings['eael_instafeed_image_count']['size']),
                'html' => $html,
            ]);
        }

        return $html;
    }
}
