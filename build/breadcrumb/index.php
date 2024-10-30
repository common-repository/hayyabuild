<?php
/**
 * simple menu block class.
*
* @since    5.0.0
* @package  hayyabuild
* @subpackage hayyabuild/includes/blocks
* @author  zintaThemes <>
*/

if (! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildBreadcrumb' )) return;

class HayyaBuildBreadcrumb extends HayyaBuildBlocks
{

  /**
   * The single instance of HayyaBuild.
   * @var  object
   * @access  private
   * @since  3.0.0
   */
  private static $_instance = false;

  /**
   * Define the Breadcrumb block class.
   *
   * @access    public
   * @since   1.0.0
   * @var     unown
   */
  public function __construct() {
    if ( self::$_instance ) {
      return self::$_instance;
    }

    return self::$_instance = true;
  } // End __construct()

  /**
   * render_callback
   *
   * @param   array   $attributes  The attributes
   *
   * @return   string  ( description_of_the_return_value )
   */
  public function render_callback( $attributes = [] ) {
    if ( empty( $attributes ) || ! is_array($attributes) ) return '';
    extract($attributes);

    $class = ! empty($classesList) ? ' '.$classesList : '';

    $title = ! empty($home_title) ? $home_title : '<i class="' . esc_attr( $home_icon ) . '"></i>';
    $main_title = empty($home_title) && empty($home_icon) ? get_bloginfo( 'name' ) : $title;

    $separator = '<i class="' . esc_attr( $separator ) . '"></i>';
    $hb_breadcrumb = '<div class="wp-block-hayyabuild-breadcrumb' . esc_attr( $class . ' ' . $id ) . '"><ul class="hayyabuild-breadcrumb">';

    if ( ! isset( $editor ) || $editor ) {
      $hb_breadcrumb .= '<li class="item-home"><a class="bread-link bread-home" href="#">' . $main_title . '</a></li>';
      $hb_breadcrumb .= '<li class="separator separator-home"> ' . $separator . ' </li>';
      $hb_breadcrumb .= '<li class="item-dash"><a class="bread-link bread-dash" href="#">'.esc_html('Dashboard', 'hayyabuild').'</a></li>';
      $hb_breadcrumb .= '<li class="separator separator-dash"> ' . $separator . ' </li>';
      $hb_breadcrumb .= '<li class="item-dash"><a class="bread-link bread-dash" href="#">'.esc_html('Post', 'hayyabuild').'</a></li>';
      $hb_breadcrumb .= '</ul></div>';
      return $hb_breadcrumb;
    }


    // If you have any custom post types with custom taxonomies, put the taxonomy name below (e.g. product_cat)
    $custom_taxonomy  = 'product_cat';
    // Get the query & post information
    global $post,$wp_query;

    $parents = '';
    // Do not display on the homepage

    if ( ! is_front_page() ) {
      // Home page
      $hb_breadcrumb .= '<li class="item-home"><a class="bread-link bread-home" href="' . get_home_url() . '" title="'.get_bloginfo('name').'">' . $main_title . '</a></li>';
      $hb_breadcrumb .= '<li class="separator separator-home"> ' . $separator . ' </li>';
      if ( is_archive() && !is_tax() && !is_category() && !is_tag() ) {
        $hb_breadcrumb .= '<li class="item-current item-archive"><strong class="bread-current bread-archive">' . post_type_archive_title( $prefix, false ) . '</strong></li>';
      } else if ( is_archive() && is_tax() && !is_category() && !is_tag() ) {
        // If post is a custom post type
        $post_type = get_post_type();
        // If it is a custom post type display name and link
        if($post_type !== 'post') {
          $post_type_object = get_post_type_object($post_type);
          $post_type_archive = get_post_type_archive_link($post_type);
          $hb_breadcrumb .= '<li class="item-cat item-custom-post-type-' . esc_attr( $post_type ) . '"><a class="bread-cat bread-custom-post-type-' . esc_attr( $post_type ) . '" href="' . esc_url( $post_type_archive ) . '" title="' . esc_attr( $post_type_object->labels->name ) . '">' . esc_html( $post_type_object->labels->name ) . '</a></li>';
          $hb_breadcrumb .= '<li class="separator"> ' . $separator . ' </li>';
        }
        $custom_tax_name = get_queried_object()->name;
        $hb_breadcrumb .= '<li class="item-current item-archive"><strong class="bread-current bread-archive">' . esc_html( $custom_tax_name ) . '</strong></li>';
      } else if ( is_single() ) {
        // If post is a custom post type
        $post_type = get_post_type();
        // If it is a custom post type display name and link
        if($post_type !== 'post') {
          $post_type_object = get_post_type_object($post_type);
          $post_type_archive = get_post_type_archive_link($post_type);
          $hb_breadcrumb .= '<li class="item-cat item-custom-post-type-' . esc_attr( $post_type ) . '"><a class="bread-cat bread-custom-post-type-' . esc_attr( $post_type ) . '" href="' . esc_url( $post_type_archive ) . '" title="' . esc_attr( $post_type_object->labels->name ) . '">' . esc_html( $post_type_object->labels->name ) . '</a></li>';
          $hb_breadcrumb .= '<li class="separator"> ' . $separator . ' </li>';
        }
        // Get post category info
        $category = get_the_category();
        if(!empty($category)) {
          // Get last category post is in
          $array_values = array_values($category);
          $last_category = end($array_values);
          // Get parent any categories and create array
          $get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','),',');
          $cat_parents = explode(',',$get_cat_parents);
          // Loop through parent categories and store in variable $cat_display
          $cat_display = '';
          foreach($cat_parents as $parents) {
            $cat_display .= '<li class="item-cat">' . $parents . '</li>';
            $cat_display .= '<li class="separator"> ' . $separator . ' </li>';
          }
        }

        // If it's a custom post type within a custom taxonomy
        $taxonomy_exists = taxonomy_exists($custom_taxonomy);
        if(empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {
          $taxonomy_terms = get_the_terms( $post->ID, $custom_taxonomy );
          $cat_id    = $taxonomy_terms[0]->term_id;
          $cat_nicename   = $taxonomy_terms[0]->slug;
          $cat_link    = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
          $cat_name    = $taxonomy_terms[0]->name;
        }

        // Check if the post is in a category
        if(!empty($last_category)) {
          $hb_breadcrumb .= $cat_display;
          $hb_breadcrumb .= '<li class="item-current item-' . esc_attr( $post->ID ) . '"><strong class="bread-current bread-' . esc_attr( $post->ID ) . '" title="' . esc_attr( get_the_title() ) . '">' . get_the_title() . '</strong></li>';
        // Else if post is in a custom taxonomy
        } else if(!empty($cat_id)) {
          $hb_breadcrumb .= '<li class="item-cat item-cat-' . esc_attr( $cat_id ) . ' item-cat-' . esc_attr( $cat_nicename ) . '"><a class="bread-cat bread-cat-' . esc_attr( $cat_id ) . ' bread-cat-' . esc_attr( $cat_nicename ) . '" href="' . esc_url( $cat_link ) . '" title="' . esc_attr( $cat_name ) . '">' . esc_html( $cat_name ) . '</a></li>';
          $hb_breadcrumb .= '<li class="separator"> ' . $separator . ' </li>';
          $hb_breadcrumb .= '<li class="item-current item-' . esc_attr( $post->ID ) . '"><strong class="bread-current bread-' . esc_attr( $post->ID ) . '" title="' . esc_attr( get_the_title() ) . '">' . get_the_title() . '</strong></li>';
        } else {
          $hb_breadcrumb .= '<li class="item-current item-' . esc_attr( $post->ID ) . '"><strong class="bread-current bread-' . esc_attr( $post->ID ) . '" title="' . esc_attr( get_the_title() ) . '">' . get_the_title() . '</strong></li>';
        }
      } else if ( is_category() ) {
        // Category page
        $hb_breadcrumb .= '<li class="item-current item-cat"><strong class="bread-current bread-cat">' . single_cat_title('', false) . '</strong></li>';
      } else if ( is_page() ) {
        // Standard page
        if( $post->post_parent ){
          // If child page, get parents
          $anc = get_post_ancestors( $post->ID );
          // Get parents in the right order
          $anc = array_reverse($anc);
          // Parent page loop
          foreach ( $anc as $ancestor ) {
            $parents .= '<li class="item-parent item-parent-' . esc_attr( $ancestor ) . '"><a class="bread-parent bread-parent-' . esc_attr( $ancestor ) . '" href="' . esc_url( get_permalink( $ancestor ) ) . '" title="' . esc_attr( get_the_title( $ancestor ) ) . '">' . get_the_title($ancestor) . '</a></li>';
            $parents .= '<li class="separator separator-' . esc_attr( $ancestor ) . '"> ' . $separator . ' </li>';
          }
          // Display parent pages
          $hb_breadcrumb .= $parents;
          // Current page
          $hb_breadcrumb .= '<li class="item-current item-' . esc_attr( $post->ID ) . '"><strong title="' . esc_attr( get_the_title() ) . '"> ' . get_the_title() . '</strong></li>';
        } else {
          // Just display current page if not parents
          $hb_breadcrumb .= '<li class="item-current item-' . esc_attr( $post->ID ) . '"><strong class="bread-current bread-' . esc_attr( $post->ID ) . '"> ' . get_the_title() . '</strong></li>';
        }
      } else if ( is_tag() ) {
        // Tag page
        // Get tag information
        $term_id    = get_query_var('tag_id');
        $taxonomy    = 'post_tag';
        $args      = 'include=' . $term_id;
        $terms      = get_terms( $taxonomy, $args );
        $get_term_id  = $terms[0]->term_id;
        $get_term_slug  = $terms[0]->slug;
        $get_term_name  = $terms[0]->name;
        // Display the tag name
        $hb_breadcrumb .= '<li class="item-current item-tag-' . esc_attr( $get_term_id ) . ' item-tag-' . esc_attr( $get_term_slug ) . '"><strong class="bread-current bread-tag-' . esc_attr( $get_term_id ) . ' bread-tag-' . esc_attr( $get_term_slug ) . '">' . esc_html( $get_term_name ) . '</strong></li>';
      } elseif ( is_day() ) {
        // Day archive
        // Year link
        $hb_breadcrumb .= '<li class="item-year item-year-' . esc_attr( get_the_time('Y') ) . '"><a class="bread-year bread-year-' . esc_attr( get_the_time('Y') ) . '" href="' . esc_attr( get_year_link( get_the_time('Y') ) ) . '" title="' . esc_attr( get_the_time('Y') ) . '">' . esc_html( get_the_time('Y') ) . ' ' . esc_html('Archive', 'hayyabuild') . '</a></li>';
        $hb_breadcrumb .= '<li class="separator separator-' . esc_attr( get_the_time('Y') ) . '"> ' . $separator . ' </li>';
        // Month link
        $hb_breadcrumb .= '<li class="item-month item-month-' . esc_attr( get_the_time('m') ) . '"><a class="bread-month bread-month-' . esc_attr( get_the_time('m') ) . '" href="' . esc_url( get_month_link( get_the_time('Y'), get_the_time('m') ) ) . '" title="' . esc_attr( get_the_time('M') ) . '">' . esc_html( get_the_time('M') ) . ' ' . esc_html('Archive', 'hayyabuild') . '</a></li>';
        $hb_breadcrumb .= '<li class="separator separator-' . esc_attr( get_the_time('m') ) . '"> ' . $separator . ' </li>';
        // Day display
        $hb_breadcrumb .= '<li class="item-current item-' . esc_attr( get_the_time('j') ) . '"><strong class="bread-current bread-' . esc_attr( get_the_time('j') ) . '"> ' . esc_html( get_the_time('jS') ) . ' ' . esc_html( get_the_time('M') ) . ' ' . esc_html('Archive', 'hayyabuild') . '</strong></li>';
      } else if ( is_month() ) {
        // Month Archive
        // Year link
        $hb_breadcrumb .= '<li class="item-year item-year-' . esc_attr( get_the_time('Y') ) . '"><a class="bread-year bread-year-' . esc_attr( get_the_time('Y') ) . '" href="' . esc_url( get_year_link( get_the_time('Y') ) ) . '" title="' . esc_attr( get_the_time('Y') ) . '">' . esc_html( get_the_time('Y') ) . ' ' . esc_html('Archive', 'hayyabuild') . '</a></li>';
        $hb_breadcrumb .= '<li class="separator separator-' . esc_attr( get_the_time('Y') ) . '"> ' . $separator . ' </li>';
        // Month display
        $hb_breadcrumb .= '<li class="item-month item-month-' . esc_attr( get_the_time('m') ) . '"><strong class="bread-month bread-month-' . esc_attr( get_the_time('m') ) . '" title="' . esc_attr( get_the_time('M') ) . '">' . esc_html( get_the_time('M') ) . ' ' . esc_html('Archive', 'hayyabuild') . '</strong></li>';
      } else if ( is_year() ) {
        // Display year archive
        $hb_breadcrumb .= '<li class="item-current item-current-' . esc_attr( get_the_time('Y') ) . '"><strong class="bread-current bread-current-' . esc_attr( get_the_time('Y') ) . '" title="' . esc_attr( get_the_time('Y') ) . '">' . esc_html( get_the_time('Y') ) . ' ' . esc_html('Archive', 'hayyabuild') . '</strong></li>';
      } else if ( is_author() ) {
        // Auhor archive
        // Get the author information
        global $author;
        $userdata = get_userdata( $author );
        // Display author name
        $hb_breadcrumb .= '<li class="item-current item-current-' . esc_attr( $userdata->user_nicename ) . '"><strong class="bread-current bread-current-' . esc_attr( $userdata->user_nicename ) . '" title="' . esc_attr( $userdata->display_name ) . '">' . esc_html('Author: ', 'hayyabuild') . esc_html( $userdata->display_name ) . '</strong></li>';
      } else if ( get_query_var('paged') ) {
        // Paginated archives
        $hb_breadcrumb .= '<li class="item-current item-current-' . esc_attr( get_query_var('paged') ) . '"><strong class="bread-current bread-current-' . esc_attr( get_query_var('paged') ) . '" title="Page ' . esc_attr( get_query_var('paged') ) . '">' . esc_html('Page') . ' ' . esc_html( get_query_var('paged') ) . '</strong></li>';
      } else if ( is_search() ) {
        // Search results page
        $hb_breadcrumb .= '<li class="item-current item-current-' . esc_attr( get_search_query() ) . '"><strong class="bread-current bread-current-' . esc_attr( get_search_query() ) . '" title="' . esc_attr( 'Search results for: ', 'hayyabuild' ) . esc_attr( get_search_query() ) . '">'. esc_html('Search results for: ', 'hayyabuild') . esc_html( get_search_query() ) . '</strong></li>';

      } elseif ( is_404() ) {
        // 404 page
        $hb_breadcrumb .= '<li>' . esc_html( 'Error 404', 'hayyabuild' ) . '</li>';
      }
    }
    $hb_breadcrumb .= '</ul></div>';
    return $hb_breadcrumb;
  } // End render_callback()

} // End HayyaBuild {} class
