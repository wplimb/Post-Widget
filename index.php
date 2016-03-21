<?php
/*
Plugin Name: Post Widget
Description: Post Widget is a WordPress widget plugin. This plugin allows you to show latest posts with thumbnail.
Plugin URI: http://wplimb.com/product/post-widget-pro
Author: WP Limb
Author URI: http://wplimb.com
Version: 1.0
*/



define( 'WPL_Post_Widget', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );

add_image_size( 'post-widget', 100, 85, TRUE );



/* Including all files */
function post_widget_css_and_script() {
	wp_enqueue_style( 'style-css', WPL_Post_Widget . 'css/style.css' );
}
add_action( 'wp_enqueue_scripts', 'post_widget_css_and_script' );




function wpl_post_widget() {
	register_widget( 'wpl_post_widget_content' );
}
add_action( 'widgets_init', 'wpl_post_widget' );

class wpl_post_widget_content extends WP_Widget {

	function wpl_post_widget_content() {
		parent::__construct( 'wpl_post_widget_content', __( 'Post Widget', 'post_widget' ),
			array(
				'description' => __( 'This widget is to display latest posts with thumbnail.', 'post_widget' )
			)
		);
	}


	//Frontend display of widget

	function widget( $args, $instance ) {
		extract( $args );

		$title    = apply_filters( 'widget_title', $instance['title'] );
		$count    = $instance['count'];
		$order_by = $instance['order_by'];
		$order = $instance['order'];

		echo $before_widget;

		$output = '';

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		global $post;

		$args = array(
			'posts_per_page' => $count,
			'orderby'        => $order_by,
			'order'          => $order,
		);



		$posts = get_posts( $args );

		if ( count( $posts ) > 0 ) {

			$output = '<div class="post-widget post-widget-theme-one">';

				foreach ( $posts as $post ): setup_postdata( $post );
						$output .= '<div class="media">';
						if ( has_post_thumbnail() ):
							$output .= '<div class="pull-left">';
							$output .= '<div class="thumb">';
							$output .= '<a href="' . get_permalink() . '">' . get_the_post_thumbnail( get_the_ID(), 'post-widget', array( 'class' => 'img-responsive' ) ) . '</a>';
							$output .= '</div>';
							$output .= '</div>';
						endif;

						$output .= '<div class="media-body">';

						$output .= '<div class="entry-meta small"><span class="sp-date">' . get_the_date('F d, Y') . '</span></div>';

						$output .= '<h2 class="entry-title"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h2>';
						$output .= '</div>';
						$output .= '</div>';

						$count++;

				endforeach;

				wp_reset_postdata();

			$output .= '</div>';

		}

		echo $output;

		echo $after_widget;
	}


	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']    = strip_tags( $new_instance['title'] );
		$instance['order_by'] = strip_tags( $new_instance['order_by'] );
		$instance['order'] = strip_tags( $new_instance['order'] );
		$instance['count']    = strip_tags( $new_instance['count'] );

		return $instance;
	}


	// Backend Widget

	function form( $instance ) {
		$defaults = array(
			'title'    => 'Post Widget',
			'order_by' => 'date',
			'order' => 'DESC',
			'count'    => 4
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title:', 'post_widget'	); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'order_by' ); ?>"><?php _e( 'Oder By:', 'post_widget' ); ?></label>
			<?php $options = array(
				'date'  => __( 'Date', 'post_widget' ),
				'title'   => __( 'Title', 'post_widget' ),
				'modified' => __( 'Modified', 'post_widget' ),
				'author' => __( 'Author', 'post_widget' ),
				'rand' => __( 'Random', 'post_widget' ),
			);
			if ( isset( $instance['order_by'] ) ) {
				$order_by = $instance['order_by'];
			}
			?>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'order_by' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'order_by' ) ); ?>">
				<?php $op = '<option value="%s"%s>%s</option>';

				foreach ( $options as $key => $value ) {

					if ( $order_by === $key ) {
						printf( $op, $key, ' selected="selected"', $value );
					} else {
						printf( $op, $key, '', $value );
					}
				} ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Oder:', 'post_widget' ); ?></label>
			<?php $options = array(
				'DESC'  => __( 'DESC', 'post_widget' ),
				'ASC'   => __( 'ASC', 'post_widget' ),
			);
			if ( isset( $instance['order'] ) ) {
				$order = $instance['order'];
			}
			?>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>">
				<?php $op = '<option value="%s"%s>%s</option>';

				foreach ( $options as $key => $value ) {

					if ( $order === $key ) {
						printf( $op, $key, ' selected="selected"', $value );
					} else {
						printf( $op, $key, '', $value );
					}
				} ?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php _e( 'Number of posts to show:', 'post_widget' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" value="<?php echo esc_attr( $instance['count'] ); ?>" style="width: 50px;margin-left: 6px;"/>
		</p>


		<?php
	}
}


