<?php
/**
 * Plugin Name: Registo de Eventos
 * Description: Regista o CPT "Eventos" e fornece o shortcode [eventos_futuros].
 * Version: 1.0
 * Author: Leonel Palma
 */
/*
 * Desenvolver um plugin WordPress simples, que permita geir e apresentar eventos. O plugin deverá criar um Costum Post Type (CPT) chamado "Eventos" e, através do plugin Advanced Costum Fields (ACF),  cada evento deverá incluir os seguintes campos: data, local e organizador. Aimagem deverá ser gerida através da funcionalidade nativa do WordPress.
 * Implementar um shortcode chamado [eventos_futuros], responsável por listar os eventos cuja data seja igual ou superior à atual. Esse shortcode deverá aceitar um parâmetro opcional limite, permitindo restringir o número de eventos apresentados. A listagem deverá ser apresentada sob a forma de grelha de três colunas.
 * O plugin deverá ainda incluir um template single para os eventos, onde serão apresntados o título, a data, o local, o organizador, a imagem de destaque e o conteúdo do evento.
 */

if (!defined('ABSPATH')){
    exit;  // Supostamente necessário! Fonte: Tutorial 
}

/*-- Registar Costum Post Type (CPT) --*/
function registo_eventos__cpt()  // Chamar a função ititulada "registo_eventos__cpt"
{
    $labels = array(
        "name" => __("Eventos", "eventos"),  // Opção por "eventos" vs "textdomain"
        "singular_name" => __("Evento", "eventos"),
        "add_new" => __("Adicionar Novo", "eventos"),
        "add_new_item" => __("Adicionar Novo Evento", "eventos"),
        "edit_item" => __("Editar Evento", "eventos"),
        "view_item" => __("Ver Evento", "eventos"),
        "search_item" => __("Pesquisar Evento", "eventos"),
        "not_found" => __("Sem Resultados", "eventos"),
        "menu_name" => __("Eventos", "eventos"),
    );

    // Requisitos do CPT
    $args = array(
        'label'=> __('Eventos', 'eventos'),
        'labels'=> $labels,
        'public'=> true,
        'show_in_rest'=> true,
        'has_archive'=> true,
        'supports'=> array('title','editor','thumbnail'),
        'rewrite'=> array('slug' => 'eventos'),
    );
    register_post_type('evento', $args );
}
add_action('init','registo_eventos__cpt');

// Campos Personalizados
if(function_exists('acf_add_local_field_group')){
    acf_add_local_group(array(
        'key'=> 'grupo_eventos',
        'title'=> 'Detalhes do Evento',
        'fields'=> array(
            array(
                'key'=> 'data_evento',
                'label'=> 'Data do Evento',
                'name'=> 'data_evento',
                'type'=> 'date_picker',
                'required'=> 1,
            ),
            array(
                'key'=> 'local_evento',
                'label'=> 'Local do Evento',
                'name'=> 'local_evento',
                'type'=> 'text',
                'required'=> 1,
            ),
            array(
                'key'=> 'organizador_evento',
                'label'=> 'Organizador do Evento',
                'name'=> 'organizador_evento',
                'type'=> 'Text',
                'required'=> 1,
            ),
        ),
        'location'=> array(
            array(
                array(
                'param'=> 'post_type',
                'operator'=> '==',
                'value'=> 'eventos',
                ),
            ),
        ),
    ));
}

/*-- Shortcode: [eventos_futuros threshold="3"] --*/
function eventos_futuros_shortcode($atts)
{
    $atts = shortcode_atts( array(
        'threshold'=> -1,  // Sem limite
        ), 
        $atts,'eventos_futuros'
        );
    
    $data_atual = current_time('Y-m-d');  // Memória da data atual
    
    $query_args = array(
        'post_type'=> 'evento',
        'posts_per_page'=> intval( $atts['threshold'] ),
        'meta_key'=> 'event_date',
        'meta_value'=> '$today',
        'meta_compare'=> '>=',
        'meta_type'=> 'DATE',
        'orderby'=> 'meta_value',
        'order'=> 'ASC',
    );  // Data igual ou superior à atual
    
    $eventos = new WP_Query($query_args);
    if ( $eventos->have_posts() ) {
        return '<p>Sem Eventos Futuros.</p>';
        }
    /* Output */
    ob_start();
?>

<div class="container">
    <div class="row">
        <?php while($eventos->have_posts() ) :$eventos->the_post();?>
        <div class="col-md-4 md-4">
            <div class="card h-100">
                <?php if(has_post_thumbnail()) : ?>
                <img src="<?php the_post_thumbnail_url('medium'); ?>"
                     class="card-img-top" alt="<?php the_title_attribute();?>" />
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title">
                        <?php the_title(); ?>
                    </h5>

                    <p class="card-text">
                                <strong>Data:</strong> <?php echo esc_html( get_field( 'data_evento' ) ); ?><br>
                                <strong>Local:</strong> <?php echo esc_html( get_field( 'local_evento' ) ); ?><br>
                                <strong>Organizador:</strong> <?php echo esc_html( get_field( 'organizador_evento' ) ); ?>

                    </p>
                </div>
            </div>
        </div>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</div>
<?php return ob_get_clean();
}
add_shortcode('eventos_futuros', 'eventos_futuros_shortcode');
    
