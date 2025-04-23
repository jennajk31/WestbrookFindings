<?php
/**
 * Template to render services input fields
 *
 * @package WC_Shipping_Australia_Post
 */

?>
<tr valign="top" id="service_options">
	<th scope="row" class="titledesc"><?php esc_html_e( 'Services', 'woocommerce-shipping-australia-post' ); ?></th>
	<td class="forminp">
		<table class="australia_post_services widefat">
			<thead>
			<th class="sort">&nbsp;</th>
			<th style="text-align:center; padding: 10px;"><?php esc_html_e( 'Service', 'woocommerce-shipping-australia-post' ); ?></th>
			<th style="text-align:center; padding: 10px;"><?php esc_html_e( 'Enable', 'woocommerce-shipping-australia-post' ); ?></th>
			<th><?php esc_html_e( 'Name', 'woocommerce-shipping-australia-post' ); ?></th>
			<th style="text-align:center; padding: 10px;"><?php esc_html_e( 'Extra Cover', 'woocommerce-shipping-australia-post' ); ?></th>
			<th style="text-align:center; padding: 10px;"><?php esc_html_e( 'Signature / Registered', 'woocommerce-shipping-australia-post' ); ?></th>
			<?php // translators: %s is currency symbol. ?>
			<th><?php echo esc_html( sprintf( __( 'Adjustment (%s)', 'woocommerce-shipping-australia-post' ), get_woocommerce_currency_symbol() ) ); ?></th>
			<th><?php esc_html_e( 'Adjustment (%)', 'woocommerce-shipping-australia-post' ); ?></th>
			</thead>
			<tbody>
			<?php
			$sort             = 0;
			$ordered_services = array();

			foreach ( $this->services as $code => $values ) {

				if ( is_array( $values ) ) {
					$name = $values['name'];
				} else {
					$name = $values;
				}

				if ( isset( $this->custom_services[ $code ] ) && isset( $this->custom_services[ $code ]['order'] ) ) {
					$sort = absint( $this->custom_services[ $code ]['order'] );
				}

				while ( isset( $ordered_services[ $sort ] ) ) {
					++$sort;
				}

				$other_service_codes = isset( $values['alternate_services'] ) ? $values['alternate_services'] : '';

				$ordered_services[ $sort ] = array( $code, $name, $other_service_codes );

				++$sort;
			}

			ksort( $ordered_services );

			foreach ( $ordered_services as $value ) {
				$code                = $value[0];
				$name                = $value[1];
				$other_service_codes = array_filter( (array) $value[2] );

				if ( ! isset( $this->custom_services[ $code ] ) ) {
					$this->custom_services[ $code ] = array();
				}

				foreach ( $other_service_codes as $key => $value ) {
					$other_service_codes[ $key ] = str_replace( $code . '_', '', $value );
				}
				?>
				<tr>
					<td class="sort"><input type="hidden" class="order"
											name="<?php echo esc_attr( "australia_post_service[$code][order]" ); ?>"
											value="<?php echo esc_attr( isset( $this->custom_services[ $code ]['order'] ) ? $this->custom_services[ $code ]['order'] : '' ); ?>"/>
					</td>
					<td style="text-align:center">
					<?php
						echo '<strong data-tip="';

						echo wc_sanitize_tooltip( $code );

					if ( $other_service_codes ) {
						echo ', ' . esc_html( implode( ', ', $other_service_codes ) );
					}

						echo '" class="tips">';

					if ( ! empty( $this->services[ $code ]['image'] ) ) {
						echo '<img src="' . esc_url( WC_SHIPPING_AUSTRALIA_POST_PLUGIN_URL . $this->services[ $code ]['image'] ) . '" alt="' . esc_attr( $this->services[ $code ]['name'] ) . '" />';
					} else {
						echo esc_html( $this->services[ $code ]['name'] );
					}

						echo '</strong>';
					?>
						</td>
					<td style="text-align:center"><input type="checkbox" name="australia_post_service[<?php echo esc_attr( $code ); ?>][enabled]" <?php checked( ( ! isset( $this->custom_services[ $code ]['enabled'] ) || ! empty( $this->custom_services[ $code ]['enabled'] ) ), true ); ?> />
					</td>
					<td><input type="text" name="australia_post_service[<?php echo esc_attr( $code ); ?>][name]" placeholder="<?php echo esc_attr( "$name ($this->title)" ); ?>" value="<?php echo esc_attr( isset( $this->custom_services[ $code ]['name'] ) ? $this->custom_services[ $code ]['name'] : '' ); ?>" size="30"/></td>
					<td style="text-align:center">
						<?php if ( in_array( $code, array_keys( $this->extra_cover ), true ) ) : ?>
							<input type="checkbox" name="australia_post_service[<?php echo esc_attr( $code ); ?>][extra_cover]" <?php checked( ( ! isset( $this->custom_services[ $code ]['extra_cover'] ) || ! empty( $this->custom_services[ $code ]['extra_cover'] ) ), true ); ?> />
						<?php endif; ?>
					</td>
					<td style="text-align:center">
						<?php if ( in_array( $code, $this->delivery_confirmation, true ) ) : ?>
							<input type="checkbox" name="australia_post_service[<?php echo esc_attr( $code ); ?>][delivery_confirmation]" <?php checked( ( ! isset( $this->custom_services[ $code ]['delivery_confirmation'] ) || ! empty( $this->custom_services[ $code ]['delivery_confirmation'] ) ), true ); ?> />
						<?php endif; ?>
					</td>
					<td><input type="text" name="australia_post_service[<?php echo esc_attr( $code ); ?>][adjustment]" placeholder="N/A" value="<?php echo esc_attr( isset( $this->custom_services[ $code ]['adjustment'] ) ? $this->custom_services[ $code ]['adjustment'] : '' ); ?>" size="4"/></td>
					<td><input type="text" name="australia_post_service[<?php echo esc_attr( $code ); ?>][adjustment_percent]" placeholder="N/A" value="<?php echo esc_attr( isset( $this->custom_services[ $code ]['adjustment_percent'] ) ? $this->custom_services[ $code ]['adjustment_percent'] : '' ); ?>" size="4"/></td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
	</td>
</tr>
