<?php
/*
Plugin Name: XMLRPC User Agent
Plugin URI: http://wordpress.org/extend/plugins/xmlrpc-user-agent/
Description: Store the user agent when posting from XMLRPC and provide functions to themes to make it visible
Author: Jorge Bernal
Version: 1.0
Author URI: http://koke.me/
*/

$xua_is_xmlrpc_new_post = false;

function xua_xmlrpc_call( $method ) {
    global $xua_is_xmlrpc_new_post;
    $xua_is_xmlrpc_new_post = true;
    echo "xua_newPost\n";
}
add_action( 'xmlrpc_call', 'xua_xmlrpc_call', 10, 1 );

function xua_insert_post( $post_ID, $post ) {
    global $xua_is_xmlrpc_new_post;
    echo "xua_insert_post\n";
    if ( $xua_is_xmlrpc_new_post && $_SERVER['HTTP_USER_AGENT'] ) {
        echo "xua_insert_post/meta\n";
        add_post_meta( $post_ID, '_xua_user_agent', $_SERVER['HTTP_USER_AGENT'], true );
    }
}
add_action( 'wp_insert_post', 'xua_insert_post', 10, 2 );

function xua_name_for_agent( $agent ) {
    $known_agents = array(
        '/wp-iphone/' => 'WordPress for iOS',
    );
    
    foreach ( $known_agents as $pattern => $name ) {
        if ( preg_match( $pattern, $agent ) ) {
            return $name;
        }
    }
    
    return $agent;
}

function xua_url_for_agent( $agent ) {
    $known_agents = array(
        '/wp-iphone/' => 'http://ios.wordpress.org/',
    );
    
    foreach ( $known_agents as $pattern => $url ) {
        if ( preg_match( $pattern, $agent ) ) {
            return $url;
        }
    }
    
    return $agent;
}

/*
** Template functions
*/

function xua_get_the_agent( $post_ID ) {
    $agent = get_post_meta( $post_ID, '_xua_user_agent', true );
    if ( $agent !== "" ) {
        $agent = xua_name_for_agent( $agent );
    }
}

function xua_get_the_agent_url( $post_ID ) {
    $agent = get_post_meta( $post_ID, '_xua_user_agent', true );
    if ( $agent !== "" ) {
        $agent = xua_url_for_agent( $agent );
    }
}

function xua_get_the_agent_link( $post_ID ) {
    $agent = get_post_meta( $post_ID, '_xua_user_agent', true );
    if ( $agent === "" ) {
        return "";
    } else {
        $name = xua_name_for_agent( $agent );
        $url = xua_url_for_agent( $agent );
        return "<a rel='nofollow' href='$url'>$name</a>";
    }
}

function xua_the_agent() {
    global $post;
    
    echo xua_get_the_agent( $post->ID );
}

function xua_the_agent_link( $before = '', $after = '' ) {
    global $post;
    
    $link = xua_get_the_agent_link( $post->ID );
    
    if ( $link != "" ) {
        echo $before . $link . $after;
    }
}