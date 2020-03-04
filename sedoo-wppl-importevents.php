<?php
/**
 * Plugin Name: Sedoo - Importation d'événements
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: Permet d'importer les événements de Events Manager vers The Events Calendar.
 * Version: 1.0
 * Author: Nicolas Gruwe
 */

$style_url = plugin_dir_url( __FILE__ ).'css/main.css';
wp_enqueue_style('import_event_style', $style_url);

require_once( ABSPATH . 'wp-admin/includes/post.php' );

function sedoo_wppl_importevents_page() {
    add_options_page('Importation événements', 'Importation événements', 'manage_options', 'sedoo-wppl-importevents', 'sedoo_wppl_importevents_page_affichage');    
}
add_action( 'admin_menu', 'sedoo_wppl_importevents_page' );

function sedoo_wppl_importevents_page_affichage() {
    global $wp; 
    $current_url = add_query_arg( array(), $wp->request);
    if(!isset($_GET['i'])) {
?>
        <div>
        <h2>Importation d'événements</h2>
        <p>Ce plugin permet d'importer les événements depuis Events Manager vers The Events Calendar, il importe les événements, les lieux, et les champs méta auteurs scientifique.</p>
        <a href="?page=sedoo-wppl-importevents&i=1">Importer</a>
        </div>
<?php 
    }
    else {
?>
    <h2>Importation d'événements</h2>
    <p>Ce plugin permet d'importer les événements depuis Events Manager vers The Events Calendar, il importe les événements, les lieux, et les champs méta auteurs scientifique.</p>
    <hr />
<?php 

        $evenements_em = get_posts([
            'post_type' => 'event',
            'post_status' => 'publish',
            'numberposts' => -1
            // 'order'    => 'ASC'
        ]);

        foreach($evenements_em as $evenement_em) {
            echo $evenement_em->ID.' - '.$evenement_em->post_title;

            // TEST SUR UN SEUL EVENEMENT 
            if($evenement_em->ID == 7031 || $evenement_em->ID == 3057  || $evenement_em->ID == 5160 ) {

                $titre_evenement = $evenement_em->post_title;
                $contenu_evenement = $evenement_em->post_content;
                $statut_evenement = $evenement_em->post_status;
                $date_debut_evenement = $evenement_em->_event_start_date; // FORMAT YYYY-MM-DD
                $date_fin_evenement = $evenement_em->_event_end_date; // FORMAT YYYY-MM-DD

                $evenement_tec = array(
                    'post_title'    => $titre_evenement,
                    'post_content'  => $contenu_evenement,
                    'post_status'   => $statut_evenement,
                    'post_author'   => 1,
                    'post_type'		=> 'tribe_events'
                );
                
                // Ajouter l'evenement events calendar en bdd
                $evenement_tec_id = wp_insert_post( $evenement_tec );
                
                // si y a une erreur on affiche qu'il y a une erreur
                if(is_wp_error($evenement_tec_id)) {
                echo '<span class="sedoo-wppl-importevents-span sedoo-wppl-importevents-error"> - Erreur : '.$evenement_tec_id->get_error_message().'</span>';
                } else {
                echo '<span class="sedoo-wppl-importevents-span sedoo-wppl-importevents-success"> - Importé</span>';
                }

                add_post_meta(  $evenement_tec_id, '_EventStartDate', $date_debut_evenement,  false );
                add_post_meta(  $evenement_tec_id, '_EventEndDate', $date_fin_evenement,  false );

                // Copier les meta auteur si il y en a (ACF fields)
                if ( metadata_exists( 'post', $evenement_em->ID, 'nom_de_lauteur_exterieur' ) ) {
                    $nom_auteure = get_post_meta( $evenement_em->ID, 'nom_de_lauteur_exterieur', true );
                    add_post_meta(  $evenement_tec_id, 'nom_de_lauteur_exterieur', $nom_auteure,  false );
                    add_post_meta(  $evenement_tec_id, '_nom_de_lauteur_exterieur', 'field_5d96f0a18037e',  false );
                }
                if ( metadata_exists( 'post', $evenement_em->ID, '_site_internet_auteur' ) ) {
                    $site_internet_auteur = get_post_meta( $evenement_em->ID, '_site_internet_auteur', true );
                    add_post_meta(  $evenement_tec_id, 'site_internet_auteur', $site_internet_auteur,  false );
                    add_post_meta(  $evenement_tec_id, '_site_internet_auteur', 'field_5d96f13280381',  false );
                }
                if ( metadata_exists( 'post', $evenement_em->ID, 'photo_auteur_exterieur' ) ) {
                    $photo_auteur_exterieur = get_post_meta( $evenement_em->ID, 'photo_auteur_exterieur', true );
                    add_post_meta(  $evenement_tec_id, 'photo_auteur_exterieur', $photo_auteur_exterieur,  false );
                    add_post_meta(  $evenement_tec_id, '_photo_auteur_exterieur', 'field_5d96f05c8037d',  false );
                }
                if ( metadata_exists( 'post', $evenement_em->ID, 'ajouteur_auteur' ) ) {
                    $ajouteur_auteur = get_post_meta( $evenement_em->ID, 'ajouteur_auteur', true );
                    add_post_meta(  $evenement_tec_id, 'ajouteur_auteur', $ajouteur_auteur,  false );
                    add_post_meta(  $evenement_tec_id, '_ajouteur_auteur', 'field_5d6fafaccec98',  false );
                }
                if ( metadata_exists( 'post', $evenement_em->ID, 'a_propos_auteur' ) ) {
                    $a_propos_auteur = get_post_meta( $evenement_em->ID, 'a_propos_auteur', true );
                    add_post_meta(  $evenement_tec_id, 'a_propos_auteur', $a_propos_auteur,  false );
                    add_post_meta(  $evenement_tec_id, '_a_propos_auteur', 'field_5d96f0fc80380',  false );
                }
                if ( metadata_exists( 'post', $evenement_em->ID, 'poste_de_lauteur' ) ) {
                    $poste_de_lauteur = get_post_meta( $evenement_em->ID, 'poste_de_lauteur', true );
                    add_post_meta(  $evenement_tec_id, 'poste_de_lauteur', $poste_de_lauteur,  false );
                    add_post_meta(  $evenement_tec_id, '_poste_de_lauteur', 'field_5d96f0be8037f',  false );
                }


                // Récupérer l'id de la location (propre au plugin, pas à wordpress)
                $id_lieu_evenement = get_post_meta($evenement_em->ID, '_location_id', true);

                global $wpdb;
                // recuperer l'id wordpress de la location
                $lieu_evenement_infos = $wpdb->get_row( "SELECT * FROM ".$wpdb->prefix."em_locations WHERE location_id = ".$id_lieu_evenement );

                // Récupérer les infos de la location
                $lieu_evenement['nom'] = $lieu_evenement_infos->location_name;
                $lieu_evenement['adresse'] = $lieu_evenement_infos->location_address;
                $lieu_evenement['ville'] = $lieu_evenement_infos->location_town;
                $lieu_evenement['cp'] = $lieu_evenement_infos->location_postcode;
                $lieu_evenement['pays'] = $lieu_evenement_infos->location_country;

                // Ajouter le lieu events calendar en bdd
                // Seulement nom existe : evenement en bdd, je crée pas et je lie direct / si non existe pas : pas en bdd, je crée en bdd et je lie
                $location_tec_trouvee = post_exists($lieu_evenement['nom'], '', '', 'tribe_venue');
                if($location_tec_trouvee == 0) {
                    $titre_location = $lieu_evenement['nom'];
                    $location_tec = array(
                        'post_title'    => $titre_location,
                        'post_type'		=> 'tribe_venue',
                        'post_status'	=> 'publish'
                    );
                    $location_tec_id = wp_insert_post( $location_tec );
                    add_post_meta( $location_tec_id, '_VenueAddress', $lieu_evenement['adresse'],  $unique = true );
                    add_post_meta( $location_tec_id, '_VenueCity', $lieu_evenement['ville'],  $unique = true );
                    add_post_meta( $location_tec_id, '_VenueCountry', $lieu_evenement['pays'],  $unique = true );
                    add_post_meta( $location_tec_id, '_VenueZip', $lieu_evenement['cp'],  $unique = true );
                } else {
                    $location_tec_id = $location_tec_trouvee;
                }
                // les lier (event et location)
                add_post_meta(  $evenement_tec_id, '_EventVenueID', $location_tec_id,  $unique = true );
            }
            echo '<hr />'; 
        }
    }
}
