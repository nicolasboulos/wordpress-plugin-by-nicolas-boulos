<?php
/*
Plugin Name: MyCalendar
Description: Ismena calendar.
Version: 1.0
Author: Nicolas B
*/

function post_event() {
    $labels = array(
        'name'               => 'Event List',
        'singular_name'      => 'Event',
        'menu_name'          => 'All Events',
        'add_new'            => 'Add New Event',
        'add_new_item'       => 'Add New Event',
        'edit_item'          => 'Edit Event',
        'new_item'           => 'Add New Event',
        'view_item'          => 'View Event',
        'search_items'       => 'Search Events',
        'not_found'          => 'Sorry, no events found',
        'not_found_in_trash' => 'Sorry, no events found in Trash',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'publicly_queryable'  => true,
        'menu_icon'           => 'dashicons-calendar',
        'supports'            => array('title', 'editor', 'thumbnail', 'custom-fields'),
    );

    register_post_type('event', $args);

    register_taxonomy(
        'event_type',
        'event',
        array(
            'label'        => 'Event Type',
            'rewrite' => array('slug' => 'add-event-type', 'with_front' => false, 'feeds' => true, 'pages' => true),
            'hierarchical' => true,
        )
    );

    add_action('save_post', 'set_default_event_category', 10, 3);
}

function set_default_event_category($post_id, $post, $update) {
    $selected_categories = get_the_terms($post_id, 'event_type');

    if (!$update && (empty($selected_categories) || is_wp_error($selected_categories) || count($selected_categories) === 0)) {
        $default_category = 'uncategorized';
        wp_set_object_terms($post_id, $default_category, 'event_type', false);
    }
}

add_action('init', 'post_event');

function mycalendar_event_list_shortcode($atts) {
    $selected_category = isset($_POST['event_category']) ? sanitize_text_field($_POST['event_category']) : '';
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    $args = array(
        'post_type'      => 'event',
        'posts_per_page' => 20,
        'paged'          => $paged,
    );

    if (!empty($selected_category)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'event_type',
                'field'    => 'slug',
                'terms'    => $selected_category,
            ),
        );
    }

    $events_query = new WP_Query($args);

    ob_start();

    $categories = get_terms(array(
        'taxonomy'   => 'event_type',
        'hide_empty' => false,
    ));

    echo '<form method="post" action="' . esc_url(get_permalink()) . '">';
    echo '<label for="event_category">Filter by Category: </label>';
    echo '<select name="event_category" id="event_category">';
    echo '<option value="">All Categories</option>';
    foreach ($categories as $category) {
        $selected = ($selected_category === $category->slug) ? 'selected' : '';
        echo '<option value="' . esc_attr($category->slug) . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
    }
    echo '</select>';
    echo '<input type="submit" value="Filter">';
    echo '</form>';

    if ($events_query->have_posts()) :
        while ($events_query->have_posts()) : $events_query->the_post();
            $event_types = get_the_terms(get_the_ID(), 'event_type');
            $event_date = get_post_meta(get_the_ID(), '_event_date', true);
            $image_size = get_post_meta(get_the_ID(), '_event_image_size', true);
            $image = wp_get_attachment_image_src(get_post_thumbnail_id(), $image_size);
            ?>
            <div class="event">
                <h2 style="color: #0000af"><u><b><?php the_title(); ?></b></u></h2>
                <div class="event-details">
                    <?php the_content(); ?>
                    <?php if (!empty($event_types)) : ?>
                        <p>Event Category:
                            <?php foreach ($event_types as $event_type) : ?>
                                <b><?php echo esc_html($event_type->name) . ''; ?></b>
                            <?php endforeach; ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($event_date) : ?>
                        <p>Event Date: <?php echo esc_html($event_date); ?></p>
                    <?php endif; ?>

                    <?php if ($image) : ?>
                        <img src="<?php echo esc_url($image[0]); ?>" alt="Event Image">
                    <?php endif; ?>
                </div>
            </div>
            <hr>
            <?php
        endwhile;

        $total_pages = $events_query->max_num_pages;
        if ($total_pages > 1) {
            $current_page = max(1, get_query_var('paged'));

            echo '<div class="pagination">';
            echo paginate_links(array(
                'base'      => get_pagenum_link(1) . '%_%',
                'format'    => 'page/%#%',
                'current'   => $current_page,
                'total'     => $total_pages,
                'prev_text' => '« Prev',
                'next_text' => 'Next »',
            ));
            echo '</div>';
        }

        wp_reset_postdata();
    else :
        echo 'No events found.';
    endif;

    return ob_get_clean();
}

add_shortcode('mycalendar_event_list', 'mycalendar_event_list_shortcode');

function mycalendar_event_image_size_meta_box() {
    add_meta_box(
        'mycalendar-event-image-size-meta-box',
        'Event Image Size',
        'mycalendar_event_image_size_meta_box_callback',
        'event',
        'normal',
        'high'
    );
    add_meta_box(
        'mycalendar-event-date-meta-box',
        'Event Date',
        'mycalendar_event_date_meta_box_callback',
        'event',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'mycalendar_event_image_size_meta_box');

function mycalendar_event_image_size_meta_box_callback($post) {
    $current_image_size = get_post_meta($post->ID, '_event_image_size', true);

    wp_nonce_field('mycalendar_event_image_size_nonce', 'mycalendar_event_image_size_nonce');

    echo '<label for="event_image_size">Select Image Size:</label>';
    echo '<select name="event_image_size" id="event_image_size">';
    
    $image_sizes = get_intermediate_image_sizes();
    foreach ($image_sizes as $size) {
        echo '<option value="' . esc_attr($size) . '" ' . selected($current_image_size, $size, false) . '>' . esc_html($size) . '</option>';
    }

    echo '</select>';
}

function mycalendar_event_date_meta_box_callback($post) {
    $current_event_date = get_post_meta($post->ID, '_event_date', true);

    wp_nonce_field('mycalendar_event_date_nonce', 'mycalendar_event_date_nonce');

    echo '<label for="event_date"><b>Event Date:</label>';
    echo '<input type="date" name="event_date" id="event_date" value="' . esc_attr($current_event_date) . '">';
}

function save_mycalendar_event_image_date_meta($post_id) {
    if (!isset($_POST['mycalendar_event_image_size_nonce']) || !isset($_POST['mycalendar_event_date_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['mycalendar_event_image_size_nonce'], 'mycalendar_event_image_size_nonce') || 
        !wp_verify_nonce($_POST['mycalendar_event_date_nonce'], 'mycalendar_event_date_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['event_image_size'])) {
        update_post_meta($post_id, '_event_image_size', sanitize_text_field($_POST['event_image_size']));
    }

    if (isset($_POST['event_date'])) {
        update_post_meta($post_id, '_event_date', sanitize_text_field($_POST['event_date']));
    }
}
add_action('save_post', 'save_mycalendar_event_image_date_meta');

function mycalendar_add_event_type_column($columns) {
    $columns['event_type'] = 'Event Type';
    return $columns;
}
add_filter('manage_event_posts_columns', 'mycalendar_add_event_type_column');

function mycalendar_custom_event_type_column($column, $post_id) {
    if ($column == 'event_type') {
        $event_types = get_the_terms($post_id, 'event_type');
        if (!empty($event_types)) {
            $event_type_names = array();
            foreach ($event_types as $event_type) {
                $event_type_names[] = $event_type->name;
            }
            echo esc_html(implode(', ', $event_type_names));
        } else {
            echo 'Uncategorized';
        }
    }
}
add_action('manage_event_posts_custom_column', 'mycalendar_custom_event_type_column', 10, 2);

function mycalendar_filter_event_by_type() {
    $screen = get_current_screen();
    if ($screen->post_type == 'event') {
        $event_types = get_terms('event_type', array('hide_empty' => false));
        if ($event_types) {
            echo '<select name="event_type_filter">';
            echo '<option value="">All Event Types</option>';
            foreach ($event_types as $event_type) {
                $selected = (isset($_GET['event_type_filter']) && $_GET['event_type_filter'] == $event_type->slug) ? 'selected' : '';
                echo '<option value="' . esc_attr($event_type->slug) . '" ' . $selected . '>' . esc_html($event_type->name) . '</option>';
            }
            echo '</select>';
        }
    }
}
add_action('restrict_manage_posts', 'mycalendar_filter_event_by_type');

function mycalendar_filter_events_by_type($query) {
    if (is_admin() && $query->is_main_query() && $query->get('post_type') == 'event') {
        $event_type_filter = isset($_GET['event_type_filter']) ? sanitize_text_field($_GET['event_type_filter']) : '';
        if (!empty($event_type_filter)) {
            $query->set('tax_query', array(
                array(
                    'taxonomy' => 'event_type',
                    'field'    => 'slug',
                    'terms'    => $event_type_filter,
                ),
            ));
        }
    }
}
add_action('pre_get_posts', 'mycalendar_filter_events_by_type');
?>
