<?php
/**
 * Add sku, author, publisher and format to product search
 */
 
// hook into wp pre_get_posts
add_action('pre_get_posts', 'woo_search_pre_get_posts');
 
/**
 * Add custom join and where statements to product search query
 * @param  mixed $q query object
 * @return void
 */
function woo_search_pre_get_posts($q){
 
    if ( is_search() ) {
        add_filter( 'posts_join', 'search_post_join' );
        add_filter( 'posts_where', 'search_post_excerpt' );
    }
}
 
/**
 * Add Custom Join Code for wp_mostmeta table
 * @param  string $join
 * @return string
 */
function search_post_join($join = ''){
 
    global $wp_the_query;
 
    // escape if not woocommerce searcg query
    if ( empty( $wp_the_query->query_vars['wc_query'] ) || empty( $wp_the_query->query_vars['s'] ) )
            return $join;
 
    $join .= "INNER JOIN wp_postmeta AS jcmt1 ON (wp_posts.ID = jcmt1.post_id)";
    return $join;
}
 
/**
 * Add custom where statement to product search query
 * @param  string $where
 * @return string
 */
function search_post_excerpt($where = ''){
 
    global $wp_the_query;
 
    // escape if not woocommerce search query
    if ( empty( $wp_the_query->query_vars['wc_query'] ) || empty( $wp_the_query->query_vars['s'] ) )
            return $where;
 
    $where = preg_replace("/post_title LIKE ('%[^%]+%')/", "post_title LIKE $1)
                OR (jcmt1.meta_key = '_sku' AND CAST(jcmt1.meta_value AS CHAR) LIKE $1)
                OR  (jcmt1.meta_key = '_author' AND CAST(jcmt1.meta_value AS CHAR) LIKE $1)
                OR  (jcmt1.meta_key = '_publisher' AND CAST(jcmt1.meta_value AS CHAR) LIKE $1)
                OR  (jcmt1.meta_key = '_format' AND CAST(jcmt1.meta_value AS CHAR) LIKE $1 ", $where);
 
    return $where;
}
