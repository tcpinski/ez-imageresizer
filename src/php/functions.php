<?php
/**
 * @author Jordan Pinski
 * 02/09/2019
 */

require 'config.php';

/**
 * Gets the header component.
 */
if ( ! function_exists( 'get_header' ) ) {

  function get_header() {
    return require_once 'components/header.php';
  }

}

/**
 * Gets the footer component.
 */
if ( ! function_exists( 'get_footer' ) ) {

  function get_footer() {
    return require_once 'components/footer.php';
  }

}

/**
 * Gets the site path
 */
if ( ! function_exists( 'get_the_path' ) ) {
  
  function get_the_path() {
    return $GLOBALS['config']['site_path'];
  }

}

/**
 * Gets the site title
 */
if ( ! function_exists( 'get_the_title' ) ) {

  function get_the_title() {
    return $GLOBALS['config']['site_title'];
  }

}

/**
 * Prints the site title
 */
if ( ! function_exists( 'the_title' ) ) {
  
  function the_title() {
    echo $GLOBALS['config']['site_title']; 
  }

}

/**
 * Prints the site logo
 */
if ( ! function_exists( 'the_logo' ) ) {

  function the_logo() {
    $the_title = get_the_title();
    $the_path = get_the_path();

    $logo = "<a href='$the_path' title='$the_title'>";
    $logo .= "<img src='dist/assets/logo.svg' alt='$the_title' />";
    $logo .= "</a>";
    echo $logo;
  }

}
