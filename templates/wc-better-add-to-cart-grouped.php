<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $product, $post;
$parent_product_post = $post;
do_action( 'woocommerce_before_add_to_cart_form' ); 
?>

    <form class="cart" method="post" enctype='multipart/form-data'>
        <table cellspacing="0" class="group_table wc-better-grouped-table">
            <tbody>
                <?php
				foreach ( $grouped_products as $product_id ) :
                    $tr_class = '';
                    $custom_qty = 0;
                
                    if($product_id == $current_grouped_product){$tr_class = 'current_prod'; $custom_qty =1;}
					if ( ! $product = wc_get_product( $product_id ) ) { continue; }
					if('yes' === get_option('woocommerce_hide_out_of_stock_items') && ! $product->is_in_stock()){ continue;}
					$post    = $product->post;
					setup_postdata( $post );
					?>
                    <tr class="<?php echo $tr_class; ?>">
                        <td>
                            <?php if ( $product->is_sold_individually() || ! $product->is_purchasable() ) : ?>
                                <?php woocommerce_template_loop_add_to_cart(); ?>
                                    <?php else : ?>
                                        <?php
									$quantites_required = true;
									woocommerce_quantity_input( array(
										'input_name'  => 'quantity[' . $product_id . ']',
										'input_value' => ( isset( $_POST['quantity'][$product_id] ) ? wc_stock_amount( sanitize_text_field($_POST['quantity'][$product_id]) ) : $custom_qty ),
										'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 0, $product ),
										'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product )
									) );
								?>
                                            <?php endif; ?>
                        </td>

                        <td class="label">
                            <label for="product-<?php echo $product_id; ?>">
                                <?php echo $product->is_visible() ? '<a href="' . esc_url( apply_filters( 'woocommerce_grouped_product_list_link', get_permalink(), $product_id ) ) . '">' . esc_html( get_the_title() ) . '</a>' : esc_html( get_the_title() ); ?>
                            </label>
                        </td>

                        <?php do_action ( 'woocommerce_grouped_product_list_before_price', $product ); ?>

                            <td class="price">
                                <?php
								echo $product->get_price_html();

								if ( $availability = $product->get_availability() ) {
									$availability_html = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>';
									echo apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );
								}
							?>
                            </td>
                    </tr>
                    <?php
				endforeach;

                $post    = $parent_product_post;
				$product = wc_get_product( $parent_product_post->ID );
				setup_postdata( $parent_product_post );
			?>
            </tbody>
        </table>

        <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $parent_product ); ?>" />

        <?php if ( $quantites_required ) : ?>
            <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
                <button type="submit" class="single_add_to_cart_button button alt">
                    <?php echo $product->single_add_to_cart_text(); ?>
                </button>
                <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
                    <?php endif; ?>
    </form>
    <?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>