const path = require('path');
const defaultConfig = require('@wordpress/scripts/config/webpack.config');

const WooCommerceDependencyExtractionWebpackPlugin = require('@woocommerce/dependency-extraction-webpack-plugin');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');

// Remove SASS rule from the default config so we can define our own.
const defaultRules = defaultConfig.module.rules.filter((rule) => {
	return String(rule.test) !== String(/\.(sc|sa)ss$/);
});

module.exports = {
	...defaultConfig,
	entry: {
		indexECM: path.resolve(process.cwd(), 'src', 'blocks', 'ECM', 'index.js'),
		indexShippingAddressValidation: path.resolve(process.cwd(), 'src', 'blocks', 'ShippingAddressValidation', 'index.js'),
		indexBillingAddressValidation: path.resolve(process.cwd(), 'src', 'blocks', 'BillingAddressValidation', 'index.js'),
		indexVAT: path.resolve(process.cwd(), 'src', 'blocks', 'VAT', 'index.js'),
		indexCheckoutMessages: path.resolve(process.cwd(), 'src', 'blocks', 'CheckoutMessages', 'index.js'),
		'ecm-links': path.resolve(
			process.cwd(),
			'src',
			'blocks',
			'ECM',
			'frontend.js'
		),
		'checkout-shipping-validation-block-frontend': path.resolve(
			process.cwd(),
			'src',
			'blocks',
			'ShippingAddressValidation',
			'frontend.js'
		),
		'checkout-billing-validation-block-frontend': path.resolve(
			process.cwd(),
			'src',
			'blocks',
			'BillingAddressValidation',
			'frontend.js'
		),
		'checkout-VAT-block-frontend': path.resolve(
			process.cwd(),
			'src',
			'blocks',
			'VAT',
			'frontend.js'
		),
		'checkout-msg-block-editor': path.resolve(
			process.cwd(),
			'src',
			'blocks',
			'CheckoutMessages',
			'index.js'
		),
	},
	module: {
		...defaultConfig.module,
		rules: [
			...defaultRules,
			{
				test: /\.(sc|sa)ss$/,
				exclude: /node_modules/,
				use: [
					MiniCssExtractPlugin.loader,
					{ loader: 'css-loader', options: { importLoaders: 1 } },
					{
						loader: 'sass-loader',
						options: {
							sassOptions: {
								includePaths: ['src/css'],
							},
						},
					},
				],
			},
		],
	},
	plugins: [
		...defaultConfig.plugins.filter(
			(plugin) =>
				plugin.constructor.name !== 'DependencyExtractionWebpackPlugin'
		),
		new WooCommerceDependencyExtractionWebpackPlugin(),
		new MiniCssExtractPlugin({
			filename: `[name].css`,
		}),
	],
};

