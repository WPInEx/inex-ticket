<?php
! defined( 'ABSPATH' ) AND exit;

//$virtual_template = new Inex_Single_Ticket_Template( 'movies' );

class Inex_Single_Ticket_Template{
    private $pt;
    private $url;
    private $path;

    /**
     * Construct
     *
     * @return void
     **/
    public function __construct( $pt )
    {
        $this->pt = $pt;
        $this->url = plugins_url( '', __FILE__ );
        $this->path = INEX_TICKET_PLUGIN_PATH . '/template/';
        add_action( 'init', array( $this, 'init_all' ) );
    }


    /**
     * Dispatch general hooks
     *
     * @return void
     **/
    public function init_all()
    {
        add_action( 'wp_enqueue_scripts',   array( $this, 'frontend_enqueue' ) );
        add_filter( 'body_class',           array( $this, 'add_body_class' ) );
        add_filter( 'template_include',     array( $this, 'custom_template' ) );
    }

    /**
     * Use for custom frontend enqueues of scripts and styles
     * http://codex.wordpress.org/Plugin_API/Action_Reference/wp_enqueue_scripts
     *
     * @return void
     **/
    public function frontend_enqueue()
    {
        global $post;
        if( $this->pt != get_post_type( $post->ID ) )
            return;
    }

    /**
     * Add custom class to body tag
     * http://codex.wordpress.org/Function_Reference/body_class
     *
     * @param array $classes
     * @return array
     **/
    public function add_body_class( $classes )
    {
        global $post;
        if( $this->pt != get_post_type( $post->ID ) )
            return $classes;

        $classes[] = $this->pt . '-body-class';
        return $classes;
    }

    /**
     * Use the plugin template file to display the CPT
     * http://codex.wordpress.org/Conditional_Tags
     *
     * @param string $template
     * @return string
     **/
    public function custom_template( $template )
    {
        $post_types = array( $this->pt );
        $theme = wp_get_theme();

        if ( is_post_type_archive( $post_types ) )
            $template = $this->path . '/inex-single-ticket-template.php';

        if ( is_singular( $post_types ) )
            $template = $this->path . 'inex-single-ticket-template.php';

        return $template;
    }

}
