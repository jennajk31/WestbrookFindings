/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};

;// CONCATENATED MODULE: external ["wc","wcBlocksRegistry"]
const external_wc_wcBlocksRegistry_namespaceObject = window["wc"]["wcBlocksRegistry"];
;// CONCATENATED MODULE: external ["wp","hooks"]
const external_wp_hooks_namespaceObject = window["wp"]["hooks"];
;// CONCATENATED MODULE: external "React"
const external_React_namespaceObject = window["React"];
;// CONCATENATED MODULE: external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/icon/index.js
/**
 * WordPress dependencies
 */


/** @typedef {{icon: JSX.Element, size?: number} & import('@wordpress/primitives').SVGProps} IconProps */

/**
 * Return an SVG icon.
 *
 * @param {IconProps}                                 props icon is the SVG component to render
 *                                                          size is a number specifiying the icon size in pixels
 *                                                          Other props will be passed to wrapped SVG component
 * @param {import('react').ForwardedRef<HTMLElement>} ref   The forwarded ref to the SVG element.
 *
 * @return {JSX.Element}  Icon component
 */
function Icon({
  icon,
  size = 24,
  ...props
}, ref) {
  return (0,external_wp_element_namespaceObject.cloneElement)(icon, {
    width: size,
    height: size,
    ...props,
    ref
  });
}
/* harmony default export */ const icon = ((0,external_wp_element_namespaceObject.forwardRef)(Icon));
//# sourceMappingURL=index.js.map
;// CONCATENATED MODULE: external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/seen.js

/**
 * WordPress dependencies
 */

const seen = (0,external_React_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_React_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M3.99961 13C4.67043 13.3354 4.6703 13.3357 4.67017 13.3359L4.67298 13.3305C4.67621 13.3242 4.68184 13.3135 4.68988 13.2985C4.70595 13.2686 4.7316 13.2218 4.76695 13.1608C4.8377 13.0385 4.94692 12.8592 5.09541 12.6419C5.39312 12.2062 5.84436 11.624 6.45435 11.0431C7.67308 9.88241 9.49719 8.75 11.9996 8.75C14.502 8.75 16.3261 9.88241 17.5449 11.0431C18.1549 11.624 18.6061 12.2062 18.9038 12.6419C19.0523 12.8592 19.1615 13.0385 19.2323 13.1608C19.2676 13.2218 19.2933 13.2686 19.3093 13.2985C19.3174 13.3135 19.323 13.3242 19.3262 13.3305L19.3291 13.3359C19.3289 13.3357 19.3288 13.3354 19.9996 13C20.6704 12.6646 20.6703 12.6643 20.6701 12.664L20.6697 12.6632L20.6688 12.6614L20.6662 12.6563L20.6583 12.6408C20.6517 12.6282 20.6427 12.6108 20.631 12.5892C20.6078 12.5459 20.5744 12.4852 20.5306 12.4096C20.4432 12.2584 20.3141 12.0471 20.1423 11.7956C19.7994 11.2938 19.2819 10.626 18.5794 9.9569C17.1731 8.61759 14.9972 7.25 11.9996 7.25C9.00203 7.25 6.82614 8.61759 5.41987 9.9569C4.71736 10.626 4.19984 11.2938 3.85694 11.7956C3.68511 12.0471 3.55605 12.2584 3.4686 12.4096C3.42484 12.4852 3.39142 12.5459 3.36818 12.5892C3.35656 12.6108 3.34748 12.6282 3.34092 12.6408L3.33297 12.6563L3.33041 12.6614L3.32948 12.6632L3.32911 12.664C3.32894 12.6643 3.32879 12.6646 3.99961 13ZM11.9996 16C13.9326 16 15.4996 14.433 15.4996 12.5C15.4996 10.567 13.9326 9 11.9996 9C10.0666 9 8.49961 10.567 8.49961 12.5C8.49961 14.433 10.0666 16 11.9996 16Z"
}));
/* harmony default export */ const library_seen = (seen);
//# sourceMappingURL=seen.js.map
;// CONCATENATED MODULE: ./resources/js/admin/blocks/constants.js
/**
 * Default inner block templates for the product collection block.
 * Exported for use in different collections, e.g., 'New Arrivals' collection.
 */
const INNER_BLOCKS_PRODUCT_TEMPLATE = ['woocommerce/product-template', {}, [['woocommerce/product-image', {
  imageSizing: 'thumbnail'
}], ['core/post-title', {
  textAlign: 'center',
  level: 3,
  fontSize: 'medium',
  style: {
    spacing: {
      margin: {
        bottom: '0.75rem',
        top: '0'
      }
    }
  },
  isLink: true,
  __woocommerceNamespace: 'core/post-title/product-title'
}], ['woocommerce/product-price', {
  textAlign: 'center',
  fontSize: 'small'
}], ['woocommerce/product-button', {
  textAlign: 'center',
  fontSize: 'small'
}]]];
const DEFAULT_QUERY = {
  perPage: 9,
  pages: 0,
  offset: 0,
  postType: 'product',
  order: 'asc',
  orderBy: 'title',
  search: '',
  exclude: [],
  inherit: null,
  taxQuery: {},
  isProductCollectionBlock: true,
  featured: false,
  woocommerceOnSale: false,
  woocommerceStockStatus: ['instock'],
  woocommerceAttributes: [],
  woocommerceHandPickedProducts: [],
  timeFrame: undefined,
  priceRange: undefined
};
const DEFAULT_ATTRIBUTES = {
  query: DEFAULT_QUERY,
  tagName: 'div',
  displayLayout: {
    type: 'grid',
    columns: 3,
    shrinkColumns: true
  }
};
;// CONCATENATED MODULE: ./resources/js/admin/blocks/product-collections/recently-viewed-products.js

/**
 * External dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Construct the inner blocks for the collection.
 */
const heading = ['core/heading', {
  textAlign: 'center',
  level: 2,
  content: (0,external_wp_i18n_namespaceObject.__)('Recently viewed products', 'woocommerce-product-recommendations'),
  style: {
    spacing: {
      margin: {
        bottom: '1rem'
      }
    }
  }
}];
const innerBlocks = [heading, INNER_BLOCKS_PRODUCT_TEMPLATE];

/**
 * Setup Attributes.
 */
const attributes = {
  displayLayout: {
    type: 'flex',
    columns: 5,
    shrinkColumns: true
  },
  query: {
    perPage: 5,
    pages: 1
  },
  hideControls: ['filterable', 'inherit', 'order', 'hand-picked', 'keyword']
};

/**
 * Handle Preview.
 */
const previewMode = {
  initialPreviewState: {
    isPreview: true,
    previewMessage: (0,external_wp_i18n_namespaceObject.__)("Actual products will depend on the shopper's journey.", 'woocommerce-product-recommendations')
  }
};

/**
 * Arguments to register the collection.
 */
const collection = {
  name: 'woocommerce-product-recommendations/product-collection/recently-viewed',
  title: (0,external_wp_i18n_namespaceObject.__)('Recently Viewed', 'woocommerce-product-recommendations'),
  icon: (0,external_React_namespaceObject.createElement)(icon, {
    icon: library_seen
  }),
  description: (0,external_wp_i18n_namespaceObject.__)('Show a list of products based on the shoppers journey.', 'woocommerce-product-recommendations'),
  keywords: ['recently viewed', 'product collection'],
  scope: ['block', 'inserter']
};

/**
 * Construct and export.
 */
const recentlyViewedCollectionData = {
  ...collection,
  attributes,
  innerBlocks,
  preview: previewMode
};
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/border.js

/**
 * WordPress dependencies
 */

const border = (0,external_React_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_React_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "m6.6 15.6-1.2.8c.6.9 1.3 1.6 2.2 2.2l.8-1.2c-.7-.5-1.3-1.1-1.8-1.8zM5.5 12c0-.4 0-.9.1-1.3l-1.5-.3c0 .5-.1 1.1-.1 1.6s.1 1.1.2 1.6l1.5-.3c-.2-.4-.2-.9-.2-1.3zm11.9-3.6 1.2-.8c-.6-.9-1.3-1.6-2.2-2.2l-.8 1.2c.7.5 1.3 1.1 1.8 1.8zM5.3 7.6l1.2.8c.5-.7 1.1-1.3 1.8-1.8l-.7-1.3c-.9.6-1.7 1.4-2.3 2.3zm14.5 2.8-1.5.3c.1.4.1.8.1 1.3s0 .9-.1 1.3l1.5.3c.1-.5.2-1 .2-1.6s-.1-1.1-.2-1.6zM12 18.5c-.4 0-.9 0-1.3-.1l-.3 1.5c.5.1 1 .2 1.6.2s1.1-.1 1.6-.2l-.3-1.5c-.4.1-.9.1-1.3.1zm3.6-1.1.8 1.2c.9-.6 1.6-1.3 2.2-2.2l-1.2-.8c-.5.7-1.1 1.3-1.8 1.8zM10.4 4.2l.3 1.5c.4-.1.8-.1 1.3-.1s.9 0 1.3.1l.3-1.5c-.5-.1-1.1-.2-1.6-.2s-1.1.1-1.6.2z"
}));
/* harmony default export */ const library_border = (border);
//# sourceMappingURL=border.js.map
;// CONCATENATED MODULE: ./resources/js/admin/blocks/product-collections/viewed-not-purchased-products.js

/**
 * External dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Construct the inner blocks for the collection.
 */
const viewed_not_purchased_products_heading = ['core/heading', {
  textAlign: 'center',
  level: 2,
  content: (0,external_wp_i18n_namespaceObject.__)('Viewed but not Bought', 'woocommerce-product-recommendations'),
  style: {
    spacing: {
      margin: {
        bottom: '1rem'
      }
    }
  }
}];
const viewed_not_purchased_products_innerBlocks = [viewed_not_purchased_products_heading, INNER_BLOCKS_PRODUCT_TEMPLATE];

/**
 * Setup Attributes.
 */
const viewed_not_purchased_products_attributes = {
  displayLayout: {
    type: 'flex',
    columns: 5,
    shrinkColumns: true
  },
  query: {
    perPage: 5,
    pages: 1
  },
  hideControls: ['filterable', 'inherit', 'order', 'hand-picked', 'keyword']
};

/**
 * Handle Preview.
 */
const viewed_not_purchased_products_previewMode = {
  initialPreviewState: {
    isPreview: true,
    previewMessage: (0,external_wp_i18n_namespaceObject.__)("Actual products will depend on the shopper's journey.", 'woocommerce-product-recommendations')
  }
};

/**
 * Arguments to register the collection.
 */
const viewed_not_purchased_products_collection = {
  name: 'woocommerce-product-recommendations/product-collection/viewed-not-purchased',
  title: (0,external_wp_i18n_namespaceObject.__)('Viewed but not Purchased', 'woocommerce-product-recommendations'),
  icon: (0,external_React_namespaceObject.createElement)(icon, {
    icon: library_border
  }),
  description: (0,external_wp_i18n_namespaceObject.__)('Display a list of recently viewed products excluding those in the current order.', 'woocommerce-product-recommendations'),
  keywords: ['recently viewed', 'product collection'],
  scope: ['block', 'inserter'],
  usesReference: ['order']
};

/**
 * Construct and export.
 */
const viewedNotPurchasedCollectionData = {
  ...viewed_not_purchased_products_collection,
  attributes: viewed_not_purchased_products_attributes,
  innerBlocks: viewed_not_purchased_products_innerBlocks,
  preview: viewed_not_purchased_products_previewMode
};
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/people.js

/**
 * WordPress dependencies
 */

const people = (0,external_React_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M15.5 9.5a1 1 0 100-2 1 1 0 000 2zm0 1.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5zm-2.25 6v-2a2.75 2.75 0 00-2.75-2.75h-4A2.75 2.75 0 003.75 15v2h1.5v-2c0-.69.56-1.25 1.25-1.25h4c.69 0 1.25.56 1.25 1.25v2h1.5zm7-2v2h-1.5v-2c0-.69-.56-1.25-1.25-1.25H15v-1.5h2.5A2.75 2.75 0 0120.25 15zM9.5 8.5a1 1 0 11-2 0 1 1 0 012 0zm1.5 0a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z",
  fillRule: "evenodd"
}));
/* harmony default export */ const library_people = (people);
//# sourceMappingURL=people.js.map
;// CONCATENATED MODULE: ./resources/js/admin/blocks/product-collections/others-also-bought.js

/**
 * External dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Construct the inner blocks for the collection.
 */
const others_also_bought_heading = ['core/heading', {
  textAlign: 'center',
  level: 2,
  content: (0,external_wp_i18n_namespaceObject.__)('Others Also Bought', 'woocommerce-product-recommendations'),
  style: {
    spacing: {
      margin: {
        bottom: '1rem'
      }
    }
  }
}];
const others_also_bought_innerBlocks = [others_also_bought_heading, INNER_BLOCKS_PRODUCT_TEMPLATE];

/**
 * Setup Attributes.
 */
const others_also_bought_attributes = {
  displayLayout: {
    type: 'flex',
    columns: 5,
    shrinkColumns: true
  },
  query: {
    perPage: 5,
    pages: 1
  },
  hideControls: ['filterable', 'inherit', 'order', 'hand-picked', 'keyword']
};

/**
 * Handle Preview.
 */
const others_also_bought_previewMode = {
  initialPreviewState: {
    isPreview: true,
    previewMessage: (0,external_wp_i18n_namespaceObject.__)('Actual products will vary depending on the product being viewed.', 'woocommerce-product-recommendations')
  }
};

/**
 * Arguments to register the collection.
 */
const others_also_bought_collection = {
  name: 'woocommerce-product-recommendations/product-collection/others-also-bought',
  title: (0,external_wp_i18n_namespaceObject.__)('Others Also Bought', 'woocommerce-product-recommendations'),
  icon: (0,external_React_namespaceObject.createElement)(icon, {
    icon: library_people
  }),
  description: (0,external_wp_i18n_namespaceObject.__)('Actual products will vary depending on the product being viewed.', 'woocommerce-product-recommendations'),
  keywords: ['others also bought', 'product collection'],
  scope: ['block', 'inserter']
};

/**
 * Construct and export.
 */
const othersAlsoBoughtCollectionData = {
  ...others_also_bought_collection,
  attributes: others_also_bought_attributes,
  innerBlocks: others_also_bought_innerBlocks,
  preview: others_also_bought_previewMode
};
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/copy-small.js

/**
 * WordPress dependencies
 */

const copySmall = (0,external_React_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  clipRule: "evenodd",
  d: "M5.625 5.5h9.75c.069 0 .125.056.125.125v9.75a.125.125 0 0 1-.125.125h-9.75a.125.125 0 0 1-.125-.125v-9.75c0-.069.056-.125.125-.125ZM4 5.625C4 4.728 4.728 4 5.625 4h9.75C16.273 4 17 4.728 17 5.625v9.75c0 .898-.727 1.625-1.625 1.625h-9.75A1.625 1.625 0 0 1 4 15.375v-9.75Zm14.5 11.656v-9H20v9C20 18.8 18.77 20 17.251 20H6.25v-1.5h11.001c.69 0 1.249-.528 1.249-1.219Z"
}));
/* harmony default export */ const copy_small = (copySmall);
//# sourceMappingURL=copy-small.js.map
;// CONCATENATED MODULE: ./resources/js/admin/blocks/product-collections/frequently-bought-together.js

/**
 * External dependencies
 */


/**
 * Internal dependencies
 */


/**
 * Construct the inner blocks for the collection.
 */
const frequently_bought_together_heading = ['core/heading', {
  textAlign: 'center',
  level: 2,
  content: (0,external_wp_i18n_namespaceObject.__)('Frequently Bought Together', 'woocommerce-product-recommendations'),
  style: {
    spacing: {
      margin: {
        bottom: '1rem'
      }
    }
  }
}];
const frequently_bought_together_innerBlocks = [frequently_bought_together_heading, INNER_BLOCKS_PRODUCT_TEMPLATE];

/**
 * Setup Attributes.
 */
const frequently_bought_together_attributes = {
  displayLayout: {
    type: 'flex',
    columns: 5,
    shrinkColumns: true
  },
  query: {
    perPage: 5,
    pages: 1
  },
  hideControls: ['filterable', 'inherit', 'order', 'hand-picked', 'keyword']
};

/**
 * Handle Preview.
 */
const frequently_bought_together_previewMode = {
  initialPreviewState: {
    isPreview: true,
    previewMessage: (0,external_wp_i18n_namespaceObject.__)('Actual products will vary depending on the product being viewed.', 'woocommerce-product-recommendations')
  }
};

/**
 * Arguments to register the collection.
 */
const frequently_bought_together_collection = {
  name: 'woocommerce-product-recommendations/product-collection/frequently-bought-together',
  title: (0,external_wp_i18n_namespaceObject.__)('Frequently Bought Together', 'woocommerce-product-recommendations'),
  icon: (0,external_React_namespaceObject.createElement)(icon, {
    icon: copy_small
  }),
  description: (0,external_wp_i18n_namespaceObject.__)('Actual products will vary depending on the product being viewed.', 'woocommerce-product-recommendations'),
  keywords: ['frequently bought together', 'product collection'],
  scope: ['block', 'inserter']
};

/**
 * Construct and export.
 */
const frequentlyBoughtTogetherCollectionData = {
  ...frequently_bought_together_collection,
  attributes: frequently_bought_together_attributes,
  innerBlocks: frequently_bought_together_innerBlocks,
  preview: frequently_bought_together_previewMode
};
;// CONCATENATED MODULE: external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: external ["wp","blockEditor"]
const external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// CONCATENATED MODULE: external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: ./resources/js/admin/blocks/assets/icons/01_plant.svg
var _g, _defs;
function _extends() { _extends = Object.assign ? Object.assign.bind() : function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

var Svg01Plant = function Svg01Plant(props) {
  return /*#__PURE__*/React.createElement("svg", _extends({
    xmlns: "http://www.w3.org/2000/svg",
    width: 43,
    height: 56,
    fill: "none"
  }, props), _g || (_g = /*#__PURE__*/React.createElement("g", {
    clipPath: "url(#01_plant_svg__a)"
  }, /*#__PURE__*/React.createElement("path", {
    fill: "#E0E0E0",
    d: "M42.193 9.546c-3.671-.353-10.153 1.435-14.332 5.383 0-6.931-3.408-13.204-6.25-14.85-2.842 1.643-6.25 7.919-6.25 14.85C11.185 10.98 4.703 9.193 1.03 9.546c-.623 7.14 5.86 19.487 14.632 22.978h11.896c8.772-3.49 15.255-15.838 14.632-22.978z"
  }), /*#__PURE__*/React.createElement("path", {
    fill: "#CCC",
    d: "M42.404 30.395H1.072v4.595h41.332z"
  }), /*#__PURE__*/React.createElement("path", {
    fill: "#CCC",
    d: "M38.958 30.395s-2.554 15.154-3.253 19.476c-.683 4.232-2.699 6.124-6.808 6.124H14.583c-4.11 0-6.123-1.892-6.809-6.124-.699-4.324-3.252-19.476-3.252-19.476h34.436"
  }), /*#__PURE__*/React.createElement("path", {
    fill: "#BBB",
    d: "M20.93 34.993H5.298c.587 3.489 1.385 8.252 1.951 11.676z"
  }))), _defs || (_defs = /*#__PURE__*/React.createElement("defs", null, /*#__PURE__*/React.createElement("clipPath", {
    id: "01_plant_svg__a"
  }, /*#__PURE__*/React.createElement("path", {
    fill: "#fff",
    d: "M.987.083h41.418v55.914H.987z"
  })))));
};

/* harmony default export */ const _01_plant = ("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDMiIGhlaWdodD0iNTYiIHZpZXdCb3g9IjAgMCA0MyA1NiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGcgY2xpcC1wYXRoPSJ1cmwoI2NsaXAwXzUyOTJfNjk1NDIpIj4KPHBhdGggZD0iTTQyLjE5MjYgOS41NDYwNEMzOC41MjE4IDkuMTkyNTIgMzIuMDM5NiAxMC45ODA3IDI3Ljg2MTEgMTQuOTI4N0MyNy44NjExIDcuOTk3ODEgMjQuNDUyOSAxLjcyNDkgMjEuNjExMSAwLjA3ODYxMzNDMTguNzY5MyAxLjcyMjMyIDE1LjM2MTIgNy45OTc4MSAxNS4zNjEyIDE0LjkyODdDMTEuMTg1MiAxMC45ODA3IDQuNzAzMDUgOS4xOTI1MiAxLjAyOTYgOS41NDYwNEMwLjQwNzE1IDE2LjY4NiA2Ljg4OTI2IDI5LjAzMzEgMTUuNjYyMiAzMi41MjQzSDI3LjU1NzVDMzYuMzMwNCAyOS4wMzMxIDQyLjgxMjUgMTYuNjg2IDQyLjE5MDEgOS41NDYwNEg0Mi4xOTI2WiIgZmlsbD0iI0UwRTBFMCIvPgo8cGF0aCBkPSJNNDIuNDAzNyAzMC4zOTQ1SDEuMDcyMjdWMzQuOTkwMkg0Mi40MDM3VjMwLjM5NDVaIiBmaWxsPSIjQ0NDQ0NDIi8+CjxwYXRoIGQ9Ik0zOC45NTc3IDMwLjM5NDVDMzguOTU3NyAzMC4zOTQ1IDM2LjQwNDEgNDUuNTQ5MSAzNS43MDUxIDQ5Ljg3MTNDMzUuMDIxNSA1NC4xMDMxIDMzLjAwNjIgNTUuOTk0NSAyOC44OTY1IDU1Ljk5NDVIMTQuNTgyOEMxMC40NzMxIDU1Ljk5NDUgOC40NjAzNiA1NC4xMDMxIDcuNzc0MTQgNDkuODcxM0M3LjA3NTE3IDQ1LjU0NjUgNC41MjE2MSAzMC4zOTQ1IDQuNTIxNjEgMzAuMzk0NUgzOC45NTUxSDM4Ljk1NzdaIiBmaWxsPSIjQ0NDQ0NDIi8+CjxwYXRoIGQ9Ik0yMC45MzA0IDM0Ljk5MzJINS4yOTc4NUM1Ljg4NDU4IDM4LjQ4MTggNi42ODMwNSA0My4yNDUyIDcuMjQ5MzcgNDYuNjY5NEwyMC45MzA0IDM0Ljk5MzJaIiBmaWxsPSIjQkJCQkJCIi8+CjwvZz4KPGRlZnM+CjxjbGlwUGF0aCBpZD0iY2xpcDBfNTI5Ml82OTU0MiI+CjxyZWN0IHdpZHRoPSI0MS40MTgyIiBoZWlnaHQ9IjU1LjkxNDMiIGZpbGw9IndoaXRlIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLjk4NzMwNSAwLjA4MjUxOTUpIi8+CjwvY2xpcFBhdGg+CjwvZGVmcz4KPC9zdmc+Cg==");
;// CONCATENATED MODULE: ./resources/js/admin/blocks/assets/icons/02_cactus.svg
var _02_cactus_g, _02_cactus_defs;
function _02_cactus_extends() { _02_cactus_extends = Object.assign ? Object.assign.bind() : function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _02_cactus_extends.apply(this, arguments); }

var Svg02Cactus = function Svg02Cactus(props) {
  return /*#__PURE__*/React.createElement("svg", _02_cactus_extends({
    xmlns: "http://www.w3.org/2000/svg",
    width: 43,
    height: 56,
    fill: "none"
  }, props), _02_cactus_g || (_02_cactus_g = /*#__PURE__*/React.createElement("g", {
    clipPath: "url(#02_cactus_svg__a)"
  }, /*#__PURE__*/React.createElement("g", {
    clipPath: "url(#02_cactus_svg__b)"
  }, /*#__PURE__*/React.createElement("path", {
    fill: "#E0E0E0",
    d: "M30.814 9.383c0 .075 0 25.678-.002 25.753H12.459l-.003-25.753c0-5.132 4.11-9.295 9.18-9.295 5.068 0 9.178 4.163 9.178 9.295"
  }), /*#__PURE__*/React.createElement("path", {
    fill: "#E0E0E0",
    d: "M7.821 12.434v-1.822c0-1.934-1.549-3.502-3.458-3.502S.904 8.678.904 10.612v3.505c0 6.476 4.116 11.622 12.045 11.622h1.828l.105-6.66c-4.8 0-7.06-2.02-7.06-6.645M35.396 12.435c0-2.213-.049-4.602-.077-6.497s1.55-3.502 3.459-3.502 3.535 1.569 3.535 3.502v8.177c0 6.479-4.116 11.625-12.045 11.625h-1.827l-.105-6.66c4.8 0 7.06-2.02 7.06-6.645"
  }), /*#__PURE__*/React.createElement("path", {
    fill: "#CCC",
    d: "M42.315 30.396H.9v4.61h41.415z"
  }), /*#__PURE__*/React.createElement("path", {
    fill: "#CCC",
    d: "M38.86 30.396S36.3 45.597 35.6 49.933c-.684 4.245-2.704 6.142-6.822 6.142H14.436c-4.118 0-6.135-1.897-6.823-6.142-.7-4.338-3.259-19.537-3.259-19.537H38.86"
  }), /*#__PURE__*/React.createElement("path", {
    fill: "#BBB",
    d: "M20.798 35.006H5.134c.588 3.5 1.388 8.278 1.956 11.713z"
  })))), _02_cactus_defs || (_02_cactus_defs = /*#__PURE__*/React.createElement("defs", null, /*#__PURE__*/React.createElement("clipPath", {
    id: "02_cactus_svg__a"
  }, /*#__PURE__*/React.createElement("path", {
    fill: "#fff",
    d: "M.9.088h41.416v55.91H.9z"
  })), /*#__PURE__*/React.createElement("clipPath", {
    id: "02_cactus_svg__b"
  }, /*#__PURE__*/React.createElement("path", {
    fill: "#fff",
    d: "M.904.088H42.32V56.08H.904z"
  })))));
};

/* harmony default export */ const _02_cactus = ("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDMiIGhlaWdodD0iNTYiIHZpZXdCb3g9IjAgMCA0MyA1NiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGcgY2xpcC1wYXRoPSJ1cmwoI2NsaXAwXzUyOTJfNjk1NTApIj4KPGcgY2xpcC1wYXRoPSJ1cmwoI2NsaXAxXzUyOTJfNjk1NTApIj4KPHBhdGggZD0iTTMwLjgxNDMgOS4zODM0MkMzMC44MTQzIDkuNDU4NDggMzAuODE0MyAzNS4wNjA2IDMwLjgxMTcgMzUuMTM1N0gxMi40NTg1QzEyLjQ1ODUgMzUuMDYwNiAxMi40NTU5IDkuNDU4NDggMTIuNDU1OSA5LjM4MzQyQzEyLjQ1NTkgNC4yNTA1NyAxNi41NjYyIDAuMDg4Mzc4OSAyMS42MzUxIDAuMDg4Mzc4OUMyNi43MDQgMC4wODgzNzg5IDMwLjgxNDMgNC4yNTA1NyAzMC44MTQzIDkuMzgzNDJaIiBmaWxsPSIjRTBFMEUwIi8+CjxwYXRoIGQ9Ik03LjgyMTQgMTIuNDM0M0M3LjgyMTQgMTIuNDM0MyA3LjgyMTQgMTAuNjMyNyA3LjgyMTQgMTAuNjEyQzcuODIxNCA4LjY3ODQ1IDYuMjcyMzcgNy4xMDk4NiA0LjM2MjkxIDcuMTA5ODZDMi40NTM0NSA3LjEwOTg2IDAuOTA0NDE5IDguNjc4NDUgMC45MDQ0MTkgMTAuNjEyVjE0LjExNjdDMC45MDQ0MTkgMjAuNTkzIDUuMDE5ODQgMjUuNzM4OCAxMi45NDkxIDI1LjczODhDMTMuOTIwNCAyNS43Mzg4IDE0Ljc1MTIgMjUuNzM4OCAxNC43NzY3IDI1LjczODhMMTQuODgxNSAxOS4wNzg4QzEwLjA4MTEgMTkuMDc4OCA3LjgyMTQgMTcuMDU5OCA3LjgyMTQgMTIuNDM0M1oiIGZpbGw9IiNFMEUwRTAiLz4KPHBhdGggZD0iTTM1LjM5NTkgMTIuNDM1MUMzNS4zOTU5IDEwLjIyMiAzNS4zNDc0IDcuODMyOSAzNS4zMTkzIDUuOTM4MThDMzUuMjkxMSA0LjA0MzQ1IDM2Ljg2ODMgMi40MzYwNCAzOC43Nzc3IDIuNDM2MDRDNDAuNjg3MiAyLjQzNjA0IDQyLjMxMjkgNC4wMDQ2MiA0Mi4zMTI5IDUuOTM4MThDNDIuMzEyOSA1Ljk1ODg4IDQyLjMxMjkgMTEuMzk0NiA0Mi4zMTI5IDE0LjExNUM0Mi4zMTI5IDIwLjU5MzggMzguMTk3NSAyNS43Mzk2IDMwLjI2ODMgMjUuNzM5NkMyOS4yOTY5IDI1LjczOTYgMjguNDY2MiAyNS43Mzk2IDI4LjQ0MDYgMjUuNzM5NkwyOC4zMzU4IDE5LjA3OTZDMzMuMTM2MyAxOS4wNzk2IDM1LjM5NTkgMTcuMDYwNiAzNS4zOTU5IDEyLjQzNTFaIiBmaWxsPSIjRTBFMEUwIi8+CjxwYXRoIGQ9Ik00Mi4zMTUxIDMwLjM5NTVIMC45MDAxNDZWMzUuMDA1NUg0Mi4zMTUxVjMwLjM5NTVaIiBmaWxsPSIjQ0NDQ0NDIi8+CjxwYXRoIGQ9Ik0zOC44NTk5IDMwLjM5NTVDMzguODU5OSAzMC4zOTU1IDM2LjMwMTIgNDUuNTk3MyAzNS42MDA4IDQ5LjkzM0MzNC45MTU4IDU0LjE3OCAzMi44OTY0IDU2LjA3NTMgMjguNzc4NCA1Ni4wNzUzSDE0LjQzNThDMTAuMzE3OCA1Ni4wNzUzIDguMzAwOTcgNTQuMTc4IDcuNjEzMzYgNDkuOTMzQzYuOTEyOTcgNDUuNTk0OCA0LjM1NDI1IDMwLjM5NTUgNC4zNTQyNSAzMC4zOTU1SDM4Ljg1NzRIMzguODU5OVoiIGZpbGw9IiNDQ0NDQ0MiLz4KPHBhdGggZD0iTTIwLjc5ODMgMzUuMDA2M0g1LjEzNDE2QzUuNzIyMDcgMzguNTA1OSA2LjUyMjE1IDQzLjI4NDEgNy4wODk2MiA0Ni43MTlMMjAuNzk4MyAzNS4wMDYzWiIgZmlsbD0iI0JCQkJCQiIvPgo8L2c+CjwvZz4KPGRlZnM+CjxjbGlwUGF0aCBpZD0iY2xpcDBfNTI5Ml82OTU1MCI+CjxyZWN0IHdpZHRoPSI0MS40MTUiIGhlaWdodD0iNTUuOTEiIGZpbGw9IndoaXRlIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLjkwMDc1NyAwLjA4Nzg5MDYpIi8+CjwvY2xpcFBhdGg+CjxjbGlwUGF0aCBpZD0iY2xpcDFfNTI5Ml82OTU1MCI+CjxyZWN0IHdpZHRoPSI0MS40MTUiIGhlaWdodD0iNTUuOTkyOCIgZmlsbD0id2hpdGUiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAuOTA0MTc1IDAuMDg3ODkwNikiLz4KPC9jbGlwUGF0aD4KPC9kZWZzPgo8L3N2Zz4K");
;// CONCATENATED MODULE: ./resources/js/admin/blocks/assets/icons/03_watering.svg
var _03_watering_g, _03_watering_defs;
function _03_watering_extends() { _03_watering_extends = Object.assign ? Object.assign.bind() : function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _03_watering_extends.apply(this, arguments); }

var Svg03Watering = function Svg03Watering(props) {
  return /*#__PURE__*/React.createElement("svg", _03_watering_extends({
    xmlns: "http://www.w3.org/2000/svg",
    width: 57,
    height: 47,
    fill: "none"
  }, props), _03_watering_g || (_03_watering_g = /*#__PURE__*/React.createElement("g", {
    clipPath: "url(#03_watering_svg__a)"
  }, /*#__PURE__*/React.createElement("path", {
    fill: "#F6F7F7",
    d: "M21.356 2.214C19.805 1.008 17.698.417 14.914.41H10.02c-2.785.008-4.892.599-6.443 1.803C1.754 3.632.83 5.781.83 8.607v9c0 10.769 6.464 16.104 11.347 18.449l1.722-4.558c-5.535-2.656-8.363-7.628-8.415-14.782V9.27c0-1.923.876-4.17 4.524-4.195h4.915c3.648.026 4.524 2.272 4.524 4.195v7.418h4.654V8.606c0-2.827-.923-4.978-2.746-6.395z"
  }), /*#__PURE__*/React.createElement("path", {
    fill: "#F6F7F7",
    d: "M42.695 41.635c-.375-2.746-2.366-17.295-3.72-27.177H14.952c-1.355 9.882-3.348 24.433-3.72 27.177-.388 2.845.873 5.366 4.842 5.366h21.781c3.969 0 5.23-2.52 4.842-5.366z"
  }), /*#__PURE__*/React.createElement("path", {
    fill: "#F0F0F0",
    d: "m56.11 23.98-8.855-8.874c-1.399-1.402-3.361-.288-3.02 1.503.323 1.686.683 3.726 1.107 5.653l-4.731 4.159-1.011-7.387H14.32l-1.29 9.416c-.028.217-.06.432-.087.645l-.037.267c-.318 2.313-.615 4.495-.874 6.376l-.038.275c-.192 1.391-.357 2.606-.489 3.58l-.127.936q-.021.168-.044.316c-.007.065-.018.13-.025.192q-.032.221-.055.399c-.01.072-.018.142-.028.202-.388 2.845.874 5.366 4.842 5.366H37.85c3.969 0 5.23-2.521 4.843-5.366-.008-.052-.447-3.275-1.014-7.405l7.29-8.333c1.927.425 3.96.785 5.642 1.109 1.787.345 2.896-1.625 1.5-3.026z"
  }))), _03_watering_defs || (_03_watering_defs = /*#__PURE__*/React.createElement("defs", null, /*#__PURE__*/React.createElement("clipPath", {
    id: "03_watering_svg__a"
  }, /*#__PURE__*/React.createElement("path", {
    fill: "#fff",
    d: "M.83.41h55.91V47H.83z"
  })))));
};

/* harmony default export */ const _03_watering = ("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTciIGhlaWdodD0iNDciIHZpZXdCb3g9IjAgMCA1NyA0NyIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGcgY2xpcC1wYXRoPSJ1cmwoI2NsaXAwXzUyOTJfNjk1NjEpIj4KPHBhdGggZD0iTTIxLjM1NjIgMi4yMTM1QzE5LjgwNSAxLjAwODY4IDE3LjY5OCAwLjQxNzkyOSAxNC45MTM2IDAuNDEwMTU2QzE0Ljg5NTUgMC40MTAxNTYgMTAuMDM3NiAwLjQxMDE1NiAxMC4wMTk1IDAuNDEwMTU2QzcuMjM1MTUgMC40MTc5MjkgNS4xMjgxMSAxLjAwODY4IDMuNTc2OTEgMi4yMTM1QzEuNzU0MjYgMy42MzA3OSAwLjgzMTI5OSA1Ljc4MTMzIDAuODMxMjk5IDguNjA1NTRDMC44MzEyOTkgOC42MDU1NCAwLjgzMTI5OSA2LjgzODQ3IDAuODMxMjk5IDE3LjYwNjdDMC44MzEyOTkgMjguMzc1IDcuMjk0NjEgMzMuNzA5OSAxMi4xNzgzIDM2LjA1NDhMMTMuOTAwMSAzMS40OTcyQzguMzY0OTMgMjguODQxNCA1LjUzNjU5IDIzLjg2OTIgNS40ODQ4OCAxNi43MTU0VjkuMjY4ODRDNS40ODQ4OCA3LjM0NjMxIDYuMzYxMzEgNS4wOTk5IDEwLjAwOTIgNS4wNzM5OUMxMC4wMjk5IDUuMDczOTkgMTQuOTAzMiA1LjA3Mzk5IDE0LjkyMzkgNS4wNzM5OUMxOC41NzE4IDUuMDk5OSAxOS40NDgyIDcuMzQ2MzEgMTkuNDQ4MiA5LjI2ODg0VjE2LjY4NjlIMjQuMTAxOFY4LjYwNTU0QzI0LjEwMTggNS43Nzg3NCAyMy4xNzg4IDMuNjI4MiAyMS4zNTYyIDIuMjEwOTFWMi4yMTM1WiIgZmlsbD0iI0Y2RjdGNyIvPgo8cGF0aCBkPSJNNDIuNjk0OCA0MS42MzUyQzQyLjMxOTkgMzguODg4NyA0MC4zMjkyIDI0LjM0MDEgMzguOTc0NSAxNC40NThIMTQuOTUxN0MxMy41OTcgMjQuMzQwMSAxMS42MDM3IDM4Ljg5MTMgMTEuMjMxNCA0MS42MzUyQzEwLjg0MzYgNDQuNDgwMSAxMi4xMDUzIDQ3LjAwMTIgMTYuMDczNyA0Ny4wMDEySDM3Ljg1NTFDNDEuODIzNiA0Ny4wMDEyIDQzLjA4NTIgNDQuNDgwMSA0Mi42OTc0IDQxLjYzNTJINDIuNjk0OFoiIGZpbGw9IiNGNkY3RjciLz4KPHBhdGggZD0iTTU2LjEwOTYgMjMuOTgwMUw0Ny4yNTQ5IDE1LjEwNTlDNDUuODU2MiAxMy43MDQxIDQzLjg5MzkgMTQuODE4MyA0NC4yMzUyIDE2LjYwODdDNDQuNTU4NCAxOC4yOTU0IDQ0LjkxNzcgMjAuMzM0NiA0NS4zNDE3IDIyLjI2MjNMNDAuNjEwNiAyNi40MjA5QzQwLjIyMjggMjMuNTgzNyAzOS44NTgzIDIwLjkyMjcgMzkuNTk5NyAxOS4wMzM5SDE0LjMyMDRDMTMuODk5IDIyLjExNDYgMTMuNDUxOCAyNS4zNzY3IDEzLjAzMDQgMjguNDQ5NkMxMy4wMDE5IDI4LjY2NzMgMTIuOTcwOSAyOC44ODIzIDEyLjk0MjUgMjkuMDk0OEMxMi45Mjk1IDI5LjE4NTUgMTIuOTE5MiAyOS4yNzM2IDEyLjkwNjMgMjkuMzYxN0MxMi41ODgzIDMxLjY3NTQgMTIuMjkxIDMzLjg1NzEgMTIuMDMyNCAzNS43MzgyQzEyLjAxOTUgMzUuODI4OSAxMi4wMDY2IDM1LjkyMjEgMTEuOTkzNyAzNi4wMTI4QzExLjgwMjMgMzcuNDA0MiAxMS42MzY5IDM4LjYxOTQgMTEuNTA1IDM5LjU5MzZDMTEuNDU4NSAzOS45MzgyIDExLjQxNDUgNDAuMjQ5MSAxMS4zNzgzIDQwLjUyOUMxMS4zNjI4IDQwLjY0MDQgMTEuMzQ5OSA0MC43NDY2IDExLjMzNDQgNDAuODQ1MUMxMS4zMjY2IDQwLjkwOTggMTEuMzE2MyA0MC45NzQ2IDExLjMwODUgNDEuMDM2OEMxMS4yODc5IDQxLjE4NDUgMTEuMjY5OCA0MS4zMTY2IDExLjI1NDMgNDEuNDM1OEMxMS4yNDM5IDQxLjUwODQgMTEuMjM2MiA0MS41NzgzIDExLjIyNTggNDEuNjM3OUMxMC44MzggNDQuNDgyOSAxMi4wOTk3IDQ3LjAwMzkgMTYuMDY4MSA0Ny4wMDM5SDM3Ljg0OTVDNDEuODE3OSA0Ny4wMDM5IDQzLjA3OTYgNDQuNDgyOSA0Mi42OTE4IDQxLjYzNzlDNDIuNjg0IDQxLjU4NjEgNDIuMjQ0NSAzOC4zNjI5IDQxLjY3ODMgMzQuMjMyOEw0OC45Njg5IDI1LjkwMDFDNTAuODk1IDI2LjMyNSA1Mi45MjcxIDI2LjY4NTEgNTQuNjEwMSAyNy4wMDlDNTYuMzk2NiAyNy4zNTM2IDU3LjUwNTcgMjUuMzg0NSA1Ni4xMDk2IDIzLjk4MjdWMjMuOTgwMVoiIGZpbGw9IiNGMEYwRjAiLz4KPC9nPgo8ZGVmcz4KPGNsaXBQYXRoIGlkPSJjbGlwMF81MjkyXzY5NTYxIj4KPHJlY3Qgd2lkdGg9IjU1LjkxMDIiIGhlaWdodD0iNDYuNTkxNyIgZmlsbD0id2hpdGUiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAuODMwNTY2IDAuNDA5MTgpIi8+CjwvY2xpcFBhdGg+CjwvZGVmcz4KPC9zdmc+Cg==");
;// CONCATENATED MODULE: ./resources/js/admin/blocks/assets/icons/04_boot.svg
var _path, _path2;
function _04_boot_extends() { _04_boot_extends = Object.assign ? Object.assign.bind() : function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _04_boot_extends.apply(this, arguments); }

var Svg04Boot = function Svg04Boot(props) {
  return /*#__PURE__*/React.createElement("svg", _04_boot_extends({
    xmlns: "http://www.w3.org/2000/svg",
    width: 47,
    height: 38,
    fill: "none"
  }, props), _path || (_path = /*#__PURE__*/React.createElement("path", {
    fill: "#F6F7F7",
    d: "M40.402 26.455C28.664 25.044 23.684 23.33 23.684 9.966V.726H.393v30.307h16.716l.342.341c2.578 2.577 5.215 4.301 9.657 4.301h19.877V33.35c0-2.474-1.856-6.323-6.583-6.891z"
  })), _path2 || (_path2 = /*#__PURE__*/React.createElement("path", {
    fill: "#F0F0F0",
    d: "M30.21 33.346c-6.379 0-10.181-2.076-12.762-4.653H.393v9.3H16.7v-4.177c2.444 2.26 5.428 4.182 10.406 4.182h19.876v-4.652H30.207zM5.867.725c.678 4.371 1.514 9.613 1.915 12.012.42 2.5 1.903 4.18 4.253 4.18s3.83-1.678 4.253-4.18c.404-2.399 1.237-7.641 1.915-12.012z"
  })));
};

/* harmony default export */ const _04_boot = ("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDciIGhlaWdodD0iMzgiIHZpZXdCb3g9IjAgMCA0NyAzOCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQwLjQwMjQgMjYuNDU1QzI4LjY2MzggMjUuMDQzNyAyMy42ODM3IDIzLjMzIDIzLjY4MzcgOS45NjYzN1YwLjcyNTU4NkgwLjM5Mjk0NFYzMS4wMzI4SDE3LjEwOTFMMTcuNDUwNyAzMS4zNzRDMjAuMDI4OCAzMy45NTEgMjIuNjY2NCAzNS42NzUxIDI3LjEwODIgMzUuNjc1MUg0Ni45ODQ4VjMzLjM0ODhDNDYuOTg0OCAzMC44NzUxIDQ1LjEyODkgMjcuMDI2MyA0MC40MDI0IDI2LjQ1NzZWMjYuNDU1WiIgZmlsbD0iI0Y2RjdGNyIvPgo8cGF0aCBkPSJNMzAuMjA5MSAzMy4zNDU2QzIzLjgzMTIgMzMuMzQ1NiAyMC4wMjg4IDMxLjI2OTkgMTcuNDQ4MiAyOC42OTI5SDAuMzkyOTQ0VjM3Ljk5MzFIMTYuNzAwMVYzMy44MTZDMTkuMTQzNiAzNi4wNzUyIDIyLjEyOCAzNy45OTgzIDI3LjEwNTYgMzcuOTk4M0g0Ni45ODIyVjMzLjM0NTZIMzAuMjA2NkgzMC4yMDkxWiIgZmlsbD0iI0YwRjBGMCIvPgo8cGF0aCBkPSJNNS44NjY4MiAwLjcyNTA5OEM2LjU0NDk5IDUuMDk2MDUgNy4zODEwNiAxMC4zMzgxIDcuNzgyMjYgMTIuNzM2OEM4LjIwMTU5IDE1LjIzNjMgOS42ODQ3NiAxNi45MTY1IDEyLjAzNTEgMTYuOTE2NUMxNC4zODU0IDE2LjkxNjUgMTUuODY1OSAxNS4yMzg5IDE2LjI4NzkgMTIuNzM2OEMxNi42OTE3IDEwLjMzODEgMTcuNTI1MSA1LjA5NjA1IDE4LjIwMzMgMC43MjUwOThINS44NjY4MloiIGZpbGw9IiNGMEYwRjAiLz4KPC9zdmc+Cg==");
;// CONCATENATED MODULE: ./resources/js/admin/blocks/inspector-controls.js

/**
 * External dependencies
 */





/**
 * Internal dependencies
 */




const ProductCollectionControls = props => {
  const {
    query
  } = props.attributes;
  const {
    setAttributes
  } = props;
  const handleRelevanceChange = value => {
    setAttributes({
      query: {
        ...props.attributes.query,
        woocommercePrlRelevanceScore: value
      }
    });
  };
  const handleFillInTheBlanksChange = value => {
    setAttributes({
      query: {
        ...props.attributes.query,
        woocommercePrlShouldFillBlanks: !props.attributes.query.woocommercePrlShouldFillBlanks
      }
    });
  };
  const possibleRelevanceValues = [{
    value: 'low',
    label: (0,external_wp_i18n_namespaceObject.__)('Low', 'woocommerce-product-recommendations')
  }, {
    value: 'medium',
    label: (0,external_wp_i18n_namespaceObject.__)('Medium', 'woocommerce-product-recommendations')
  }, {
    value: 'high',
    label: (0,external_wp_i18n_namespaceObject.__)('High', 'woocommerce-product-recommendations')
  }];
  const relevance = query?.woocommercePrlRelevanceScore || 'low';
  const fillInTheBlanks = !!query?.woocommercePrlShouldFillBlanks || false;
  return (0,external_React_namespaceObject.createElement)(external_React_namespaceObject.Fragment, null, (0,external_React_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.InspectorControls, null, (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: (0,external_wp_i18n_namespaceObject.__)('Relevance', 'woocommerce-product-recommendations')
  }, (0,external_React_namespaceObject.createElement)("div", {
    className: "wc-prl-block-product-collection__relevance"
  }, (0,external_React_namespaceObject.createElement)("img", {
    src: _01_plant,
    alt: (0,external_wp_i18n_namespaceObject.__)('Plant icon', 'woocommerce-product-recommendations')
  }), (0,external_React_namespaceObject.createElement)("img", {
    src: _02_cactus,
    alt: (0,external_wp_i18n_namespaceObject.__)('Cactus icon', 'woocommerce-product-recommendations')
  }), (0,external_React_namespaceObject.createElement)("img", {
    src: _03_watering,
    alt: (0,external_wp_i18n_namespaceObject.__)('Watering icon', 'woocommerce-product-recommendations')
  }), (0,external_React_namespaceObject.createElement)("img", {
    src: _04_boot,
    alt: (0,external_wp_i18n_namespaceObject.__)('Boot icon', 'woocommerce-product-recommendations')
  })), (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControl, {
    isBlock: true,
    help: (0,external_wp_i18n_namespaceObject.__)('Shoppers will see only the most relevant recommendations.', 'woocommerce-product-recommendations'),
    value: relevance,
    onChange: handleRelevanceChange
  }, possibleRelevanceValues.map(option => (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControlOption, {
    key: option.value,
    label: option.label,
    value: option.value
  }))), (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.ToggleControl, {
    label: (0,external_wp_i18n_namespaceObject.__)('Fill in the blanks', 'woocommerce-product-recommendations'),
    help: (0,external_wp_i18n_namespaceObject.__)('Include less relevant or random items if too few or no products fit the criteria.', 'woocommerce-product-recommendations'),
    checked: fillInTheBlanks,
    onChange: value => handleFillInTheBlanksChange(value)
  }))));
};
const withProductCollectionControls = (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(BlockEdit => {
  return props => {
    const {
      name
    } = props;
    if (name !== 'woocommerce/product-collection' || props.attributes.collection !== 'woocommerce-product-recommendations/product-collection/frequently-bought-together' && props.attributes.collection !== 'woocommerce-product-recommendations/product-collection/others-also-bought') {
      return (0,external_React_namespaceObject.createElement)(BlockEdit, {
        ...props
      });
    }
    return (0,external_React_namespaceObject.createElement)(external_React_namespaceObject.Fragment, null, (0,external_React_namespaceObject.createElement)(ProductCollectionControls, {
      ...props
    }), (0,external_React_namespaceObject.createElement)(BlockEdit, {
      ...props
    }));
  };
}, 'withProductCollectionControls');
;// CONCATENATED MODULE: ./resources/js/admin/blocks/index.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */






/**
 * Register product collection types.
 */
(0,external_wc_wcBlocksRegistry_namespaceObject.__experimentalRegisterProductCollection)(recentlyViewedCollectionData);
(0,external_wc_wcBlocksRegistry_namespaceObject.__experimentalRegisterProductCollection)(viewedNotPurchasedCollectionData);
(0,external_wc_wcBlocksRegistry_namespaceObject.__experimentalRegisterProductCollection)(frequentlyBoughtTogetherCollectionData);
(0,external_wc_wcBlocksRegistry_namespaceObject.__experimentalRegisterProductCollection)(othersAlsoBoughtCollectionData);

/**
 * Edit inspector controls for the product collections.
 */
(0,external_wp_hooks_namespaceObject.addFilter)('editor.BlockEdit', 'woocommerce-product-recommendations', withProductCollectionControls);
/******/ })()
;