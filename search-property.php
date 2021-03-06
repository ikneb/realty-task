<?php
/*
 * Ajax for search property
 */

define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');
/*require_once ('../simple-fields/functions.php');*/

if (isset($_POST)) {
    print_r(get_property());
}

/*
 * Function that check meta data
 * and do query for search property
 *
 */
function get_property()
{
    $page = !empty($_POST['page']) ? $_POST['page'] : 1;
    $limit = 5;
    $posts = wp_count_posts('property');
    $count_posts = $posts->publish;
    $offset = $count_posts - ($limit * $page);

    if ($count_posts % 10 == 0) {
        $pages = ($count_posts / $limit);
    } else {
        $pages = ($count_posts / $limit + 1);
    }

    if (($limit * $page) > $count_posts) {
        $offset = 0;
        $limit = $count_posts % $limit;
    }

    $meta = '';


    if (!empty($_POST['name'])) {
        $name = htmlspecialchars($_POST['name']);
        $meta .= "meta_value='$name' ";
    }
    if (!empty($_POST['coordinates'])) {
        $coordinates = htmlspecialchars($_POST['coordinates']);
        $meta .= "meta_value='$coordinates' ";
    }
    if (!empty($_POST['number_floors'])) {
        $number_floors = htmlspecialchars($_POST['number_floors']);
        $meta .= "meta_value='$number_floors' ";
    }
    if (!empty($_POST['type'])) {
        $type = htmlspecialchars($_POST['type']);
        $meta .= "meta_value='$type' ";
    }
    if (!empty($_POST['area'])) {
        $area = htmlspecialchars($_POST['area']);
        $meta .= "meta_value='$area' ";
    }
    if (!empty($_POST['number_rooms'])) {
        $number_rooms = htmlspecialchars($_POST['number_rooms']);
        $meta .= "meta_value='$number_rooms' ";
    }
    if (!empty($_POST['balcony'])) {
        $balcony = htmlspecialchars($_POST['balcony']);
        $meta .= "meta_value='$balcony' ";
    }
    if (!empty($_POST['bathroom'])) {
        $bathroom = htmlspecialchars($_POST['bathroom']);
        $meta .= "meta_value='$bathroom' ";
    }

    $meta = str_replace(" ", " OR ", $meta);
    $meta = substr($meta, 0, -3);

    global $wpdb;
    $sql = "SELECT post_id FROM $wpdb->postmeta WHERE $meta";
    $query_id = $meta_pages = $wpdb->get_results($sql, 'OBJECT');
    $array_id = array();
    foreach ($query_id as $id) {
        $items =$id->post_id;

        array_push($array_id,$items );
    }
    print_r($array_id);
    $query = new WP_Query(array(
            'post_type' => 'property',
            'post__in' => $array_id
    ));

    if ($query->have_posts()) : ?>

        <?php while ($query->have_posts()) :
            $query->the_post(); ?>
            <hr>
            <div class="search__block">
                <?php
                $image_property = simple_fields_value("image_property");
                $name = simple_fields_value("name");
                $number_floors = simple_fields_value("number_floors");
                $type = simple_fields_value("type");
                ?>
                <img src="<?php echo $image_property['url']; ?>">
                <h3><a href="<?php echo get_permalink(); ?>"><?php echo $name; ?></a></h3>
                <p>
                    <span class="search__type">Type: <?php echo $type['selected_radiobutton']['value']; ?></span>
                    <span class="search__floors">Floors :<?php echo $number_floors['selected_option']['value']; ?></span>
                </p>
            </div>
        <?php endwhile; ?>
        <div class="page-block">
            <?php for ($i = 1; $i <= $pages; $i++): ?>

                <?php if ($i == $page): ?><b><?= $i ?></b>
                <?php else: ?><a class="page" href="<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>

            <?php endfor; ?>
        </div>

    <?php else: ?>
        <div><h1>Not found</h1></div>
    <?php endif;

}
