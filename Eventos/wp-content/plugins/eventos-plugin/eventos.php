<?php
/**
 * Plugin Name: Registo de Eventos
 * Description: Regista o CPT "Eventos" e fornece o shortcode [eventos_futuros].
 * Version: 1.0
 * Author: Leonel Palma
 */
/*
 * Desenvolver um plugin WordPress simples, que permita geir e apresentar eventos. O plugin dever� criar um Costum Post Type (CPT) chamado "Eventos" e, atrav�s do plugin Advanced Costum Fields (ACF),  cada evento dever� incluir os seguintes campos: data, local e organizador. Aimagem dever� ser gerida atrav�s da funcionalidade nativa do WordPress.
 * Implementar um shortcode chamado [eventos_futuros], respons�vel por listar os eventos cuja data seja igual ou superior � atual. Esse shortcode dever� aceitar um par�metro opcional limite, permitindo restringir o n�mero de eventos apresentados. A listagem dever� ser apresentada sob a forma de grelha de tr�s colunas.
 * O plugin dever� ainda incluir um template single para os eventos, onde ser�o apresntados o t�tulo, a data, o local, o organizador, a imagem de destaque e o conte�do do evento.
 */
 
/*-- Registar Costum Post Type (CPT) --*/
function registo_eventos__cpt()  // Chamar a fun��o ititulada "registo_eventos__cpt"
{
    $labels = array(
        "name" => __("Eventos", "eventos"),
        "singular_name" => __("Evnto", "eventos"),
        "add_new" => __("Adicionar Novo", "eventos"),
        "add_new_item" => __("Adicionar Novo Evento", "eventos"),
        "edit_item" => __("Editar Evento", "eventos"),
        "view_item" => __("Ver Evento", "eventos"),
        "search_item" => __("Pesquisar Evento", "eventos"),
        "not_found" => __("Sem Resultados", "eventos"),
        "menu_name" => __("Eventos", "eventos"),
    );

    $args = array(
        'label'=> __('Eventos', 'eventos'),
        'labels'=> $labels,
        'public'=> true,
        'show_in_rest'=> true,
        'has_archive'=> true,
        'supports'=> array('title','editor','thumbnail'),
        'rewrite'=> array('slug' => 'eventos'),);
register_post_type('evento', $args );
}
add_action('init','registo_eventos__cpt');


/*-- Shortcode: [eventos_futuros threshold="3"] --*/ 
function eventos_futuros_shortcode($atts) {
    $atts = shortcode_atts( array(
        'threshold'=> -1,  // Sem limite
        ), $atts,'eventos_futuros');
    $today = current_time('Y-m-d');  // Mem�ria da data atual
    $query_args = array(
        'post_type'=> 'evento',
        'posts_per_page'=> intval( $atts['threshold'] ),
        'meta_key'=> 'event_date',
        'meta_value'=> '$today',
        'meta_compare'=> '>=',
        'meta_type'=> 'meta_value',
        'order'=> 'ASC',
    );  // Data igual ou superior � atual
    $events = new WP_Query($query_args);
    if ( $events->have_posts() ) {
        return '<p>Sem Eventos Futuros.</p>';
        }
    /* Output */
    ob_start();
?>